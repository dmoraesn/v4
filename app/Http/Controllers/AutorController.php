<?php

namespace App\Http\Controllers;

use App\Services\Sapl\SaplAutoresService;
use Illuminate\Support\Facades\Cache;

class AutorController extends Controller
{
    public function index(string $cidade)
    {
        $cidades = Cache::get('cidades');

        abort_unless(isset($cidades[$cidade]), 404);

        $cidadeData = $cidades[$cidade];
        $cidadeData['slug'] = $cidade;

        $saplService = new SaplAutoresService($cidadeData['sapl']);

        $autores = Cache::remember(
            "cidade:{$cidade}:autores",
            now()->addHours(6),
            fn () => $saplService->listarAutores()
        );

        return view('cidade.autores', [
            'cidade'  => $cidadeData,
            'autores' => $autores,
        ]);
    }
}
