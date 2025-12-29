<?php

namespace App\Services;

use App\Repositories\CidadeRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BuscaGlobalService
{
    protected CidadeRepository $cidadeRepository;

    public function __construct(CidadeRepository $cidadeRepository)
    {
        $this->cidadeRepository = $cidadeRepository;
    }

    public function buscar(string $q, int $page = 1, int $perPage = 10): array
    {
        $cacheKey = 'busca:global:' . md5($q) . ':page:' . $page;

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($q, $page, $perPage) {

            $resultados = [];
            $cidades = $this->cidadeRepository->all();
            $qNormalizado = mb_strtolower(trim($q));

            foreach ($cidades as $slug => $cidade) {
                try {
                    $sapl = new SaplService($cidade['sapl']);
                    $response = $sapl->buscarMaterias($q, 1);

                    foreach ($response['results'] as $materia) {

                        $ementa = $materia['ementa'] ?? '';
                        $texto  = $materia['texto_integral'] ?? '';

                        // 🔒 FILTRO REAL DE RELEVÂNCIA
                        if (
                            !Str::contains(mb_strtolower($ementa), $qNormalizado) &&
                            !Str::contains(mb_strtolower($texto), $qNormalizado)
                        ) {
                            continue; // descarta resultado irrelevante
                        }

                        $resultados[] = [
                            'id'     => $materia['id'],
                            'numero' => $materia['numero'] ?? '',
                            'ano'    => $materia['ano'] ?? '',
                            'ementa' => $this->highlight($ementa, $q),
                            'cidade' => [
                                'slug' => $slug,
                                'nome' => $cidade['nome'],
                                'uf'   => $cidade['uf'],
                            ],
                        ];
                    }
                } catch (\Throwable $e) {
                    // Fail-safe: ignora cidade com erro
                    continue;
                }
            }

            $total = count($resultados);
            $offset = ($page - 1) * $perPage;

            return [
                'dados' => array_slice($resultados, $offset, $perPage),
                'total' => $total,
                'page'  => $page,
                'total_pages' => max(1, (int) ceil($total / $perPage)),
            ];
        });
    }

    /**
     * Highlight seguro da palavra-chave
     */
    protected function highlight(string $text, string $q): string
    {
        if ($text === '' || $q === '') {
            return e($text);
        }

        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        return preg_replace(
            '/' . preg_quote($q, '/') . '/iu',
            '<mark class="bg-yellow-200 font-semibold">$0</mark>',
            $escaped
        );
    }
}
