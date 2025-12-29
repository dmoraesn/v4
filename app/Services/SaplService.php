<?php

namespace App\Services;

use App\Services\Sapl\AutorResolver;
use App\Services\Sapl\SaplClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SaplService
{
    protected string $baseUrl;
    protected int $pageSize = 10;

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
     * Listagem padrão de matérias (sem busca).
     */
    public function listarMaterias(int $page = 1): array
    {
        $resultado = $this->safeRequest(
            cacheKey: "sapl:listar:{$this->baseUrl}:{$page}",
            ttl: now()->addMinutes(3),
            endpoint: '/materia/materialegislativa/',
            params: [
                'page' => $page,
                'page_size' => $this->pageSize,
                'o' => '-data_apresentacao',
            ]
        );

        return $this->enriquecerMateriasComAutor($resultado);
    }

    /**
     * Busca semântica local (texto + autoria).
     */
    public function buscaSemanticaLocal(string $q, int $page = 1): array
    {
        // 1️⃣ Busca textual direta
        $resultado = $this->buscarMateriasPorTexto($q, $page);

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
    public function buscarMateriasPorTexto(string $q, int $page = 1): array
    {
        if ($q === '') {
            return $this->emptyResponse($page);
        }

        return $this->safeRequest(
            cacheKey: "sapl:buscar:texto:" . md5($this->baseUrl . $q . $page),
            ttl: now()->addMinutes(5),
            endpoint: '/materia/materialegislativa/',
            params: [
                'search' => $q,
                'page' => $page,
                'page_size' => $this->pageSize,
                'o' => '-data_apresentacao',
            ]
        );
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

        return $this->safeRequest(
            cacheKey: "sapl:buscar:ids:" . md5($this->baseUrl . $ids . $page),
            ttl: now()->addMinutes(10),
            endpoint: '/materia/materialegislativa/',
            params: [
                'id__in' => $ids,
                'page' => $page,
                'page_size' => $this->pageSize,
                'o' => '-data_apresentacao',
            ]
        );
    }

    /**
     * Detalhe da matéria.
     */
    public function materia(int $id): array
    {
        return Cache::remember(
            "sapl:materia:{$this->baseUrl}:{$id}",
            now()->addMinutes(30),
            function () use ($id) {
                try {
                    return Http::timeout(8)
                        ->retry(2, 300)
                        ->get("{$this->baseUrl}/materia/materialegislativa/{$id}/")
                        ->json() ?? [];
                } catch (Throwable $e) {
                    Log::error('Erro SAPL materia', [
                        'id' => $id,
                        'error' => $e->getMessage(),
                    ]);
                    return [];
                }
            }
        );
    }

    /**
     * Autores da matéria (interface pública correta).
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

        $response = $this->safeRequest(
            cacheKey: "sapl:buscar:autoria:" . md5($this->baseUrl . $q),
            ttl: now()->addMinutes(10),
            endpoint: '/materia/autoria/',
            params: [
                'search' => $q,
                'page_size' => 50,
            ]
        );

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
            $autores = $this->autorResolver
                ->resolveAutoresDaMateria($materia['id']);

            $materia['autor'] = $autores[0] ?? null;
            $materia['autores'] = $autores;
        }

        return $resultado;
    }

    /* =====================================================
     * CORE HTTP + NORMALIZAÇÃO
     * ===================================================== */

    protected function safeRequest(
        string $cacheKey,
        $ttl,
        string $endpoint,
        array $params
    ): array {
        return Cache::remember($cacheKey, $ttl, function () use ($endpoint, $params) {
            try {
                $response = Http::timeout(8)
                    ->retry(2, 300)
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
        });
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
