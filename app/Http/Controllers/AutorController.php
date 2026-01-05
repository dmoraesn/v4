<?php

namespace App\Http\Controllers;

use App\Models\Cidade;
use App\Models\Parlamentar;
use Illuminate\Support\Facades\Cache;

class AutorController extends Controller
{
    /**
     * Exibe a lista de parlamentares da cidade.
     *
     * Agora consulta diretamente o banco local (tabela parlamentar),
     * filtrando apenas os ativos e ordenando alfabeticamente.
     */
    public function index(string $cidadeSlug)
    {
        $cidade = Cidade::where('slug', $cidadeSlug)->firstOrFail();

        // Parlamentares ativos da cidade (ordenados por nome)
        $parlamentares = Parlamentar::where('cidade_id', $cidade->id)
            ->where('ativo', true)
            ->orderBy('nome_parlamentar')
            ->get();

        $parlamentaresAtivos = $parlamentares->count();

        return view('cidade.autores', [
            'cidade' => $cidade,
            'parlamentares' => $parlamentares,
            'parlamentaresAtivos' => $parlamentaresAtivos,
        ]);
    }
}