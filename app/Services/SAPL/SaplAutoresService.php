<?php

namespace App\Services\Sapl;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class SaplAutoresService
{
    protected SaplClient $client;

    public function __construct(string $saplUrl)
    {
        $saplUrl = rtrim($saplUrl, '/');
        if (!str_ends_with($saplUrl, '/api')) {
            $saplUrl .= '/api';
        }
        $this->client = new SaplClient($saplUrl);
    }

    /**
     * Busca a legislatura atual (priorizando marcada como "(Atual)" ou a mais recente).
     *
     * @return int|null ID da legislatura atual
     */
    public function getLegislaturaAtualId(): ?int
    {
        return Cache::remember(
            'sapl:legislatura_atual:' . md5($this->client->getBaseApiUrl()),
            now()->addDays(1),
            function () {
                try {
                    $response = $this->client->get('/parlamentares/legislatura/');

                    if (empty($response['results'])) {
                        return null;
                    }

                    $legislaturas = collect($response['results']);

                    // Prioriza explicitamente marcada como "(Atual)"
                    $atual = $legislaturas->first(fn ($leg) => str_contains($leg['__str__'] ?? '', '(Atual)'));

                    if ($atual) {
                        return (int) $atual['id'];
                    }

                    // Fallback: maior ID (mais recente)
                    return (int) $legislaturas->sortByDesc('id')->first()['id'];
                } catch (\Throwable $e) {
                    Log::warning('Falha ao obter legislatura atual do SAPL', ['error' => $e->getMessage()]);
                    return null;
                }
            }
        );
    }

    /**
     * Busca parlamentares da legislatura atual com filtro ?legislatura={id}.
     *
     * @return array
     */
    protected function fetchParlamentaresLegislaturaAtual(): array
    {
        $legislaturaId = $this->getLegislaturaAtualId();

        if (!$legislaturaId) {
            Log::warning('Legislatura atual não encontrada – usando fallback por campo ativo');
            return $this->fetchAllParlamentares(generic: true);
        }

        return $this->fetchAllParlamentares(legislaturaId: $legislaturaId);
    }

    /**
     * Busca parlamentares com paginação completa e filtro opcional.
     *
     * @param int|null $legislaturaId
     * @param bool $generic Usar sem filtro de legislatura
     * @return array
     */
    protected function fetchAllParlamentares(?int $legislaturaId = null, bool $generic = false): array
    {
        $parlamentares = [];
        $page = 1;
        $pageSize = 100;

        $endpoint = '/parlamentares/parlamentar/';
        $fallbackEndpoint = '/base/parlamentar/';

        do {
            $params = [
                'page'      => $page,
                'page_size' => $pageSize,
            ];

            if ($legislaturaId && !$generic) {
                $params['legislatura'] = $legislaturaId;
            }

            $data = $this->client->get($endpoint, $params);

            if (!$data || !isset($data['results'])) {
                $data = $this->client->get($fallbackEndpoint, $params);

                if (!$data || !isset($data['results'])) {
                    break;
                }
            }

            $results = $data['results'] ?? [];
            $parlamentares = array_merge($parlamentares, $results);

            $totalPages = $data['pagination']['total_pages'] ?? 1;
            $page++;
        } while ($page <= $totalPages);

        return $parlamentares;
    }

    /**
     * Lista todos os parlamentares mapeados (uso interno).
     *
     * @return array
     */
    public function listarAutores(): array
    {
        $rawParlamentares = $this->fetchParlamentaresLegislaturaAtual();

        return collect($rawParlamentares)->map(function ($autor) {
            $partido = Arr::get($autor, 'partido.sigla', 'Sem partido');

            return [
                'id'         => $autor['id'],
                'nome'       => $autor['nome_parlamentar'] ?? $autor['nome_completo'] ?? 'Sem nome',
                'ativo'      => $autor['ativo'] ?? false,
                'partido'    => $partido,
                'url'        => $autor['id']
                    ? rtrim($this->client->getBasePublicUrl(), '/') . '/parlamentar/' . $autor['id']
                    : null,
                'fotografia' => $autor['fotografia'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Lista apenas parlamentares ativos da legislatura atual.
     *
     * @return array
     */
    public function listarAutoresAtivos(): array
    {
        $autores = $this->listarAutores();

        $ativos = array_filter($autores, fn($autor) => $autor['ativo']);

        // Limite de segurança
        return array_slice(array_values($ativos), 0, 100);
    }

    /**
     * Retorna o total de parlamentares ativos da legislatura atual.
     *
     * @return int
     */
    public function totalAutoresAtivos(): int
    {
        return count($this->listarAutoresAtivos());
    }

    /**
     * Ranking dos top parlamentares ativos por quantidade de matérias apresentadas.
     *
     * Otimizado para cidades grandes (ex: Manaus com >50k autorias):
     * - Adiciona filtro de data recente (desde o início da legislatura atual - 1 ano para cobrir transição).
     * - Limite máximo de 50 páginas (~10k registros processados).
     *
     * @param int $limit Quantidade a retornar (padrão 5)
     * @return array
     */
    public function listarAutoresAtivosComQuantidadeMaterias(int $limit = 5): array
    {
        return Cache::remember(
            'sapl:autores_rank_quantidade:' . md5($this->client->getBaseApiUrl()),
            now()->addHours(6),
            function () use ($limit) {
                try {
                    $parlamentaresAtivos = collect($this->listarAutoresAtivos())
                        ->keyBy('id')
                        ->toArray();

                    if (empty($parlamentaresAtivos)) {
                        return [];
                    }

                    $idsParlamentares = array_keys($parlamentaresAtivos);

                    // Filtro por data: matérias recentes (ajusta para cobrir legislatura atual + transição)
                    $dataInicio = now()->subYears(2)->format('Y-m-d'); // Cobertura segura para legislaturas

                    $autorias = [];
                    $page = 1;
                    $pageSize = 200;
                    $maxPages = 50; // Limite rigoroso para evitar timeout (~10k autorias)

                    do {
                        $response = $this->client->get('/materia/autoria/', [
                            'page'         => $page,
                            'page_size'    => $pageSize,
                            'data_apresentacao__gte' => $dataInicio, // Filtro chave para reduzir volume
                        ]);

                        if (!$response || !isset($response['results'])) {
                            break;
                        }

                        $results = $response['results'] ?? [];
                        if (empty($results)) {
                            break;
                        }

                        $autorias = array_merge($autorias, $results);

                        $totalPages = $response['pagination']['total_pages'] ?? 1;

                        $page++;
                        if ($page > $maxPages) {
                            Log::info('Limite de segurança de páginas atingido no ranking de autorias SAPL', [
                                'max_pages' => $maxPages,
                                'cidade'    => $this->client->getBaseApiUrl()
                            ]);
                            break;
                        }
                    } while ($page <= $totalPages);

                    $contagem = array_fill_keys($idsParlamentares, 0);

                    foreach ($autorias as $autoria) {
                        $parlamentarId = $autoria['autor'] ?? null;
                        if ($parlamentarId && isset($contagem[$parlamentarId])) {
                            $contagem[$parlamentarId]++;
                        }
                    }

                    $resultado = [];
                    foreach ($parlamentaresAtivos as $id => $parlamentar) {
                        $resultado[] = [
                            'nome'                => $parlamentar['nome'],
                            'partido'             => $parlamentar['partido'],
                            'url'                 => $parlamentar['url'],
                            'fotografia'          => $parlamentar['fotografia'],
                            'quantidade_materias' => $contagem[$id],
                        ];
                    }

                    usort($resultado, fn($a, $b) => $b['quantidade_materias'] <=> $a['quantidade_materias']);

                    return array_slice($resultado, 0, $limit);
                } catch (\Throwable $e) {
                    Log::error('Erro crítico ao calcular ranking de vereadores no SAPL', ['error' => $e->getMessage()]);
                    return [];
                }
            }
        );
    }
}