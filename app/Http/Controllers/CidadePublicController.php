<?php

namespace App\Http\Controllers;

use App\Repositories\CidadeRepository;
use Illuminate\View\View;

class CidadePublicController extends Controller
{
    public function __construct(
        protected CidadeRepository $cidadeRepository
    ) {}

    /**
     * Listagem pública de todas as cidades indexadas
     *
     * @return View
     */
    public function index(): View
    {
        $cidades = $this->cidadeRepository->all();

        // Ordena alfabeticamente pelo nome da cidade
        uasort($cidades, fn ($a, $b) => strcmp($a['nome'], $b['nome']));

        return view('cidades.index', [
            'cidades' => $cidades,
        ]);
    }
}