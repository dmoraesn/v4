<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\MateriaLegislativa;
use App\Models\Parlamentar;

class CidadeController extends Controller
{
    /**
     * Exibe o dashboard da cidade específica.
     *
     * Utiliza dados do banco local sincronizado:
     * - Total de matérias e leis/normas.
     * - Parlamentares ativos.
     * - Top 5 parlamentares por quantidade de matérias apresentadas.
     * - Leis mais relevantes (fallback para termos estruturais).
     *
     * @param string $cidade Slug da cidade
     */
    public function show(string $cidade)
    {
        /** @var Cidade $cidadeModel */
        $cidadeModel = Cidade::where('slug', $cidade)->firstOrFail();

        // Total de matérias
        $totalMaterias = MateriaLegislativa::where('cidade_id', $cidadeModel->id)->count();

        // Total de leis e normas (tipos relevantes)
        $totalLeisNormas = MateriaLegislativa::where('cidade_id', $cidadeModel->id)
            ->whereIn('tipo_sigla', ['LEI', 'LCP', 'DEC', 'EMENDA', 'RES'])
            ->count();

        // Total de parlamentares ativos
        $totalParlamentaresAtivos = Parlamentar::where('cidade_id', $cidadeModel->id)
            ->where('ativo', true)
            ->count();

        // Top 5 parlamentares por quantidade de matérias apresentadas
        $topParlamentares = Parlamentar::where('cidade_id', $cidadeModel->id)
            ->with(['filiacaoAtual'])
            ->withCount('materias') // Usa o relacionamento materias()
            ->orderByDesc('materias_count')
            ->limit(5)
            ->get();

        // Leis mais acessadas (ou fallback estrutural)
        $leisMaisAcessadas = MateriaLegislativa::where('cidade_id', $cidadeModel->id)
            ->orderByDesc('acessos')
            ->limit(10)
            ->get();

        // Fallback caso não haja acessos registrados
        if ($leisMaisAcessadas->isEmpty()) {
            $leisMaisAcessadas = collect([
                (object) ['id' => null, 'ementa' => 'Lei Orgânica do Município', 'tipo_sigla' => 'Lei Orgânica'],
                (object) ['id' => null, 'ementa' => 'Plano Diretor Participativo', 'tipo_sigla' => 'Lei Complementar'],
                (object) ['id' => null, 'ementa' => 'Código Tributário Municipal', 'tipo_sigla' => 'Lei Complementar'],
            ]);
        }

        return view('cidade.home', [
            'cidade'                  => $cidadeModel,
            'totalMaterias'           => $totalMaterias,
            'totalLeisNormas'         => $totalLeisNormas,
            'totalParlamentaresAtivos'=> $totalParlamentaresAtivos,
            'topParlamentares'        => $topParlamentares,
            'leisMaisAcessadas'       => $leisMaisAcessadas,
        ]);
    }
}