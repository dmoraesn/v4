<?php

namespace App\Http\Controllers;

use App\Repositories\CidadeRepository;
use Illuminate\Http\Request;

class SugestaoController extends Controller
{
    public function __construct(
        protected CidadeRepository $cidadeRepository
    ) {}

    public function cidades(Request $request)
    {
        $q = strtolower($request->get('q', ''));

        $cidades = $this->cidadeRepository->all();

        $result = collect($cidades)
            ->filter(fn ($cidade) => str_contains(strtolower($cidade['nome']), $q))
            ->map(fn ($cidade, $slug) => [
                'nome' => $cidade['nome'],
                'slug' => $slug,
                'url'  => route('cidade.home', $slug),
            ])
            ->values();

        return response()->json($result);
    }
}