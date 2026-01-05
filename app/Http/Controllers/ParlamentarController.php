<?php

namespace App\Http\Controllers;

use App\Models\Parlamentar;
use App\Models\MateriaLegislativa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParlamentarController extends Controller
{
    /**
     * Exibe o perfil público individual do parlamentar.
     *
     * @param string $cidade Slug da cidade
     * @param string $parlamentar Slug do parlamentar (nome_parlamentar normalizado)
     * @return View
     */
    public function show(string $cidade, string $parlamentar): View
    {
        // Busca o parlamentar pelo slug da cidade e nome normalizado
        $parlamentarModel = Parlamentar::whereHas('cidade', fn ($query) => $query->where('slug', $cidade))
            ->whereRaw("LOWER(REPLACE(nome_parlamentar, ' ', '-')) = ?", [strtolower($parlamentar)])
            ->with(['filiacaoAtual', 'cidade'])
            ->firstOrFail();

        // Incrementa o contador de acessos ao perfil
        $parlamentarModel->incrementarAcesso();

        // Total de matérias apresentadas pelo parlamentar (incluindo co-autoria)
        $totalMaterias = MateriaLegislativa::whereHas('parlamentares', fn ($query) => $query->where('parlamentar_id', $parlamentarModel->id))
            ->count();

        // Matérias mais recentes apresentadas (limitado a 10)
        $materiasRecentes = MateriaLegislativa::whereHas('parlamentares', fn ($query) => $query->where('parlamentar_id', $parlamentarModel->id))
            ->with('cidade')
            ->orderByDesc('data_apresentacao')
            ->take(10)
            ->get();

        return view('parlamentar.show', [
            'parlamentar'       => $parlamentarModel,
            'totalMaterias'     => $totalMaterias,
            'totalAcessos'      => $parlamentarModel->acessos,
            'materiasRecentes'  => $materiasRecentes,
        ]);
    }
}