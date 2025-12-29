<?php

namespace App\Services\Sapl;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class AutorResolver
{
    protected SaplClient $client;

    /**
     * content_type_id => definição
     * Estes IDs são reais no SAPL
     */
    private const CONTENT_TYPE_MAP = [

        // Parlamentar (vereador)
        38 => [
            'api'   => '/parlamentar/parlamentar/',
            'rota'  => 'parlamentar',
            'label' => 'Parlamentar',
        ],

        // Órgão interno
        16 => [
            'api'   => '/materia/orgao/',
            'rota'  => 'orgao',
            'label' => 'Órgão',
        ],

        // Comissão
        22 => [
            'api'   => '/comissoes/comissao/',
            'rota'  => 'comissao',
            'label' => 'Comissão',
        ],

        // Bancada
        24 => [
            'api'   => '/parlamentar/bancada/',
            'rota'  => 'bancada',
            'label' => 'Bancada',
        ],

        // Bloco
        25 => [
            'api'   => '/parlamentar/bloco/',
            'rota'  => 'bloco',
            'label' => 'Bloco Parlamentar',
        ],

        // Frente
        27 => [
            'api'   => '/parlamentar/frente/',
            'rota'  => 'frente',
            'label' => 'Frente Parlamentar',
        ],
    ];

    public function __construct(SaplClient $client)
    {
        $this->client = $client;
    }

    /**
     * Resolve todos os autores reais de uma matéria.
     */
    public function resolveAutoresDaMateria(int $materiaId): array
    {
        return Cache::remember(
            $this->cacheKey($materiaId),
            now()->addMinutes(30),
            function () use ($materiaId) {
                try {
                    $autorias = $this->client->get('/materia/autoria/', [
                        'materia' => $materiaId,
                    ]);

                    if (empty($autorias['results'])) {
                        return [];
                    }

                    $resultado = [];

                    foreach ($autorias['results'] as $autoria) {
                        $autor = $this->resolverAutor($autoria);
                        if ($autor) {
                            $resultado[] = $autor;
                        }
                    }

                    return $resultado;

                } catch (Throwable $e) {
                    Log::error('Erro ao resolver autores da matéria', [
                        'materia_id' => $materiaId,
                        'error' => $e->getMessage(),
                    ]);

                    return [];
                }
            }
        );
    }

    /**
     * Resolve UM autor (robusto).
     */
    protected function resolverAutor(array $autoria): ?array
    {
        if (empty($autoria['autor'])) {
            return null;
        }

        try {
            $autorBase = $this->client->get(
                "/base/autor/{$autoria['autor']}/"
            );

            if (empty($autorBase['id'])) {
                return null;
            }

            $contentType = $autorBase['content_type'] ?? null;
            $objectId    = $autorBase['object_id'] ?? null;

            $map = self::CONTENT_TYPE_MAP[$contentType] ?? null;

            $detalhe = null;
            if ($map && $objectId) {
                try {
                    $detalhe = $this->client->get(
                        $map['api'] . $objectId . '/'
                    );
                } catch (Throwable $e) {
                    // detalhe é opcional
                }
            }

            return [
                'id'             => $autorBase['id'],
                'nome'           => $autorBase['nome'] ?? 'Autor não identificado',
                'tipo'           => $map['label'] ?? 'Autor Externo',
                'primeiro_autor' => (bool) ($autoria['primeiro_autor'] ?? false),

                // opcionais
                'cargo'   => $detalhe['cargo'] ?? null,
                'partido' => $detalhe['partido']['sigla'] ?? null,

                // link público só quando existir
                'url' => ($map && $objectId)
                    ? $this->buildPublicUrl($map['rota'], (int) $objectId)
                    : null,

                'content_type' => $contentType,
                'object_id'    => $objectId,
            ];

        } catch (Throwable $e) {
            Log::warning('Erro ao resolver autor', [
                'autor_id' => $autoria['autor'],
                'error'    => $e->getMessage(),
            ]);

            return null;
        }
    }

    /* =====================================================
     * UTILITÁRIOS
     * ===================================================== */

    protected function buildPublicUrl(string $rota, int $objectId): string
    {
        return rtrim($this->client->getBasePublicUrl(), '/')
            . "/{$rota}/{$objectId}";
    }

    protected function cacheKey(int $materiaId): string
    {
        return sprintf(
            'sapl:autorresolver:%s:%d',
            $this->client->getBaseApiUrl(),
            $materiaId
        );
    }
}
