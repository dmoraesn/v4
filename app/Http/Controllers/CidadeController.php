<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Cidade;
use App\Services\SaplService;

class CidadeController extends Controller
{
    /**
     * Dashboard da Cidade.
     *
     * Exibe estatísticas básicas e entrada para busca legislativa.
     *
     * @param string $cidade Slug da cidade (ex: fortaleza)
     */
    public function show(string $cidade)
    {
        /** @var Cidade $cidadeModel */
        $cidadeModel = Cidade::where('slug', $cidade)->firstOrFail();

        /**
         * Estatísticas da cidade (cache 6 horas).
         * Nesta fase, mantemos mock parcial + dados reais onde já existem.
         */
        $stats = Cache::remember(
            "cidade:{$cidadeModel->id}:stats",
            now()->addHours(6),
            function () use ($cidadeModel) {
                try {
                    // Base já real
                    $totalLeis = $cidadeModel->total_leis;

                    // Etapas futuras:
                    // - total_autores via AutorResolver
                    // - total_normas específicas
                    return [
                        'total_materias' => $totalLeis,
                        'total_leis'     => $totalLeis,
                        'total_autores'  => 0, // placeholder consciente
                    ];
                } catch (\Throwable $e) {
                    logger()->warning(
                        "Falha ao montar stats da cidade {$cidadeModel->slug}",
                        ['error' => $e->getMessage()]
                    );

                    return [
                        'total_materias' => 0,
                        'total_leis'     => 0,
                        'total_autores'  => 0,
                    ];
                }
            }
        );

        /**
         * Enviamos o model diretamente para a view.
         * O accessor `brasao_url` já resolve tudo.
         */
        return view('cidade.home', [
            'cidade' => [
                'id'     => $cidadeModel->id,
                'slug'   => $cidadeModel->slug,
                'nome'   => $cidadeModel->nome,
                'uf'     => $cidadeModel->uf,
                'sapl'   => $cidadeModel->sapl,
                'brasao' => $cidadeModel->brasao_url,
            ],
            'stats' => $stats,
        ]);
    }
}
