<?php

namespace App\Services;

use App\Services\Sapl\AutorResolver;
use App\Services\Sapl\SaplClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SaplService
{
    protected string $baseUrl;
    protected int $pageSize = 100; // Otimizado para sincronizações em batch
    protected SaplClient $client;
    protected AutorResolver $autorResolver;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        if (!str_ends_with($this->baseUrl, '/api')) {
            $this->baseUrl .= '/api';
        }
        /**
         * Cliente HTTP central
         */
        $this->client = new SaplClient($this->baseUrl);
        /**
         * Resolver de autores (parlamentar, órgão, comissão, etc.)
         */
        $this->autorResolver = new AutorResolver($this->client);
    }

    /* =====================================================
     * INTERFACE PÚBLICA
     * ===================================================== */

    /**
     * Listagem padrão de matérias (sem busca) - otimizada para sincronização completa.
     */
    public function listarMaterias(int $page = 1): array
    {
        $endpoint = '/materia/materialegislativa/';
        $params = [
            'page' => $page,
            'page_size' => $this->pageSize,
            'o' => '-data_apresentacao',
        ];

        return $this->requestSemCache($endpoint, $params);
    }

    /**
     * Listagem exclusiva de parlamentares.
     */
    public function listarParlamentares(int $page = 1): array
    {
        $endpoint = '/parlamentares/parlamentar/';
        $params = [
            'page' => $page,
            'page_size' => $this->pageSize,
            'o' => 'nome_parlamentar', // Ordenação alfabética
        ];

        return $this->requestSemCache($endpoint, $params);
    }

    /**
     * Detalhe individual de parlamentar.
     *
     * Útil para capturar dados detalhados (como partido) que podem não estar
     * presentes na listagem paginada.
     * Não usa cache para evitar dependência da tabela cache (problema atual).
     */
    public function parlamentar(int $id): array
    {
        try {
            $response = Http::timeout(8)
                ->connectTimeout(5)
                ->retry(3, 500)
                ->get("{$this->baseUrl}/parlamentares/parlamentar/{$id}/");

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('SAPL detalhe parlamentar inválido', [
                'id' => $id,
                'status' => $response->status(),
            ]);
        } catch (Throwable $e) {
            Log::error('Erro SAPL detalhe parlamentar', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Listagem de filiações partidárias.
     */
    public function listarFiliacoes(int $page = 1): array
    {
        $endpoint = '/parlamentares/filiacao/';
        $params = [
            'page' => $page,
            'page_size' => $this->pageSize,
            'o' => '-data_filiacao',
        ];

        return $this->requestSemCache($endpoint, $params);
    }

    /**
     * Busca semântica local (texto + autoria) - mantém cache curto para buscas de usuário.
     */
    public function buscaSemanticaLocal(string $q, int $page = 1, int $pageSize = null): array
    {
        // 1️⃣ Busca textual direta
        $resultado = $this->buscarMateriasPorTexto($q, $page, $pageSize);
        // 2️⃣ Fallback por autoria
        if (empty($resultado['results'])) {
            $materiaIds = $this->buscarMateriaIdsPorAutoria($q);
            if (!empty($materiaIds)) {
                $resultado = $this->buscarMateriasPorIds($materiaIds, $page);
                $resultado['fonte'] = 'autoria';
            }
        }
        return $this->enriquecerMateriasComAutor($resultado);
    }

    /**
     * Busca textual simples.
     */
    public function buscarMateriasPorTexto(string $q, int $page = 1, int $pageSize = null): array
    {
        if ($q === '') {
            return $this->emptyResponse($page);
        }
        $pageSize = $pageSize ?? $this->pageSize;
        return $this->requestSemCache('/materia/materialegislativa/', [
            'search' => $q,
            'page' => $page,
            'page_size' => $pageSize,
            'o' => '-data_apresentacao',
        ]);
    }

    /**
     * Busca matérias por múltiplos IDs.
     */
    public function buscarMateriasPorIds(array $ids, int $page = 1): array
    {
        if (empty($ids)) {
            return $this->emptyResponse($page);
        }
        $ids = implode(',', array_unique($ids));
        return $this->requestSemCache('/materia/materialegislativa/', [
            'id__in' => $ids,
            'page' => $page,
            'page_size' => $this->pageSize,
            'o' => '-data_apresentacao',
        ]);
    }

    /**
     * Detalhe da matéria legislativa.
     */
    public function materia(int $id): array
    {
        try {
            $response = Http::timeout(5)
                ->connectTimeout(5)
                ->retry(3, 500)
                ->get("{$this->baseUrl}/materia/materialegislativa/{$id}/");

            return $response->json() ?? [];
        } catch (Throwable $e) {
            Log::error('Erro SAPL materia', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Autores da matéria (interface pública).
     */
    public function getAutoresDaMateria(int $materiaId): array
    {
        return $this->autorResolver->resolveAutoresDaMateria($materiaId);
    }

    /* =====================================================
     * AUTORIA / SEMÂNTICA
     * ===================================================== */
    protected function buscarMateriaIdsPorAutoria(string $q): array
    {
        if ($q === '') {
            return [];
        }
        $response = $this->requestSemCache('/materia/autoria/', [
            'search' => $q,
            'page_size' => 50,
        ]);
        return collect($response['results'] ?? [])
            ->pluck('materia')
            ->unique()
            ->values()
            ->all();
    }

    /* =====================================================
     * ENRIQUECIMENTO DE DADOS
     * ===================================================== */
    protected function enriquecerMateriasComAutor(array $resultado): array
    {
        if (empty($resultado['results'])) {
            return $resultado;
        }
        foreach ($resultado['results'] as &$materia) {
            $autores = $this->autorResolver->resolveAutoresDaMateria($materia['id']);
            $materia['autor'] = $autores[0] ?? null;
            $materia['autores'] = $autores;
        }
        return $resultado;
    }

    /* =====================================================
     * CORE HTTP + NORMALIZAÇÃO
     * ===================================================== */
    protected function requestSemCache(string $endpoint, array $params): array
    {
        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(3, 500)
                ->get($this->baseUrl . $endpoint, $params);

            if (!$response->successful()) {
                Log::warning('SAPL resposta inválida', [
                    'endpoint' => $endpoint,
                    'status' => $response->status(),
                ]);
                return $this->emptyResponse((int) ($params['page'] ?? 1));
            }

            return $this->normalizar(
                raw: $response->json(),
                page: (int) ($params['page'] ?? 1)
            );
        } catch (Throwable $e) {
            Log::error('Erro SAPL request', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return $this->emptyResponse((int) ($params['page'] ?? 1));
        }
    }

    protected function normalizar(array $raw, int $page): array
    {
        $pagination = $raw['pagination'] ?? [];
        return [
            'results' => $raw['results'] ?? [],
            'pagination' => [
                'page' => $pagination['page'] ?? $page,
                'total_pages' => $pagination['total_pages'] ?? 0,
                'total_entries' => $pagination['total_entries'] ?? 0,
                'next_page' => $pagination['next_page'] ?? null,
                'previous_page' => $pagination['previous_page'] ?? null,
            ],
        ];
    }

    protected function emptyResponse(int $page): array
    {
        return [
            'results' => [],
            'pagination' => [
                'page' => $page,
                'total_pages' => 0,
                'total_entries' => 0,
                'next_page' => null,
                'previous_page' => null,
            ],
        ];
    }
}