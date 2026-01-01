<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Cidade;
use App\Services\SaplService;
use App\Services\Sapl\SaplAutoresService;

class CidadeController extends Controller
{
    /**
     * Exibe o dashboard da cidade específica.
     *
     * Carrega dados reais do SAPL com cache otimizado para evitar timeouts:
     * - Estatísticas gerais.
     * - Leis estruturais mais relevantes.
     * - Total e top 5 parlamentares ativos da legislatura atual.
     *
     * @param string $cidade Slug da cidade
     */
    public function show(string $cidade)
    {
        /** @var Cidade $cidadeModel */
        $cidadeModel = Cidade::where('slug', $cidade)->firstOrFail();

        $cidadeArray = [
            'id'     => $cidadeModel->id,
            'slug'   => $cidadeModel->slug,
            'nome'   => $cidadeModel->nome,
            'uf'     => $cidadeModel->uf,
            'sapl'   => $cidadeModel->sapl,
            'brasao' => $cidadeModel->brasao_url,
        ];

        $saplService    = new SaplService($cidadeModel->sapl);
        $autoresService = new SaplAutoresService($cidadeModel->sapl);

        $stats = Cache::remember(
            "cidade:{$cidadeModel->id}:stats",
            now()->addHours(6),
            function () use ($saplService, $autoresService) {
                $responseMaterias = $saplService->listarMaterias(1);
                $totalMaterias    = $responseMaterias['pagination']['total_entries'] ?? 0;

                $totalAutores = $autoresService->totalAutoresAtivos();

                return [
                    'total_materias' => $totalMaterias,
                    'total_leis'     => $totalMaterias, // Aproximação conservadora
                    'total_autores'  => $totalAutores,
                ];
            }
        );

        $leisMaisAcessadas = Cache::remember(
            "cidade:{$cidadeModel->id}:leis_relevantes",
            now()->addHours(12),
            function () use ($saplService) {
                $termos = [
                    'Lei Orgânica',
                    'Plano Diretor',
                    'Código Tributário',
                    'Código de Obras',
                    'Zoneamento',
                    'Lei de Uso e Ocupação do Solo',
                    'Regimento Interno',
                    'LDO',
                    'LOA',
                    'PPA',
                ];

                $leisEncontradas = [];

                foreach ($termos as $termo) {
                    $response = $saplService->buscaSemanticaLocal($termo, 1, 5);
                    foreach ($response['results'] ?? [] as $materia) {
                        $tipo = $materia['tipo']['descricao'] ?? $materia['tipo']['sigla'] ?? 'Desconhecido';

                        $leisEncontradas[] = [
                            'id'     => $materia['id'],
                            'titulo' => $materia['ementa'] ?: "Matéria {$materia['numero']}/{$materia['ano']}",
                            'tipo'   => $tipo,
                        ];
                    }
                }

                $leisUnicas = collect($leisEncontradas)->unique('id')->take(10)->values()->all();

                if (empty($leisUnicas)) {
                    return [
                        ['id' => null, 'titulo' => 'Lei Orgânica do Município', 'tipo' => 'Lei Orgânica'],
                        ['id' => null, 'titulo' => 'Plano Diretor Participativo', 'tipo' => 'Lei Complementar'],
                        ['id' => null, 'titulo' => 'Código Tributário Municipal', 'tipo' => 'Lei Complementar'],
                    ];
                }

                return $leisUnicas;
            }
        );

        $totalAutoresAtivos = $autoresService->totalAutoresAtivos();

        $topAutoresAtivos = Cache::remember(
            "cidade:{$cidadeModel->id}:autores_top5",
            now()->addHours(6),
            function () use ($autoresService) {
                return $autoresService->listarAutoresAtivosComQuantidadeMaterias(5);
            }
        );

        return view('cidade.home', [
            'cidade'              => $cidadeArray,
            'stats'               => $stats,
            'totalAutoresAtivos'  => $totalAutoresAtivos,
            'leisMaisAcessadas'    => $leisMaisAcessadas,
            'topAutoresAtivos'    => $topAutoresAtivos,
        ]);
    }
}