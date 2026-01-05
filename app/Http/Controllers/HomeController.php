<?php

namespace App\Http\Controllers;

use App\Repositories\CidadeRepository;
use App\Services\BuscaGlobalService;
use App\Models\MateriaLegislativa;
use App\Models\Cidade;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    protected CidadeRepository $cidadeRepository;

    public function __construct(CidadeRepository $cidadeRepository)
    {
        $this->cidadeRepository = $cidadeRepository;
    }

    public function index(): View
    {
        // 1. Total global de documentos
        $totalLeis = Cache::remember('home.total_leis', 3600, function () {
            return MateriaLegislativa::count();
        });

        // 2. Total de cidades cadastradas
        $totalCidades = Cache::remember('home.total_cidades', 3600, function () {
            return Cidade::count();
        });

        // 3. Top 5 cidades (Nome ajustado para $cidadesPrincipais para bater com sua view)
        $cidadesPrincipais = Cache::remember('home.top_cidades', 3600, function () {
            return Cidade::query()
                ->where('total_leis_local', '>', 0)
                ->orderByDesc('total_leis_local')
                ->take(5)
                ->get();
        });

        // 4. Top 5 matérias mais acessadas
        $materiasMaisAcessadas = Cache::remember('home.materias_mais_acessadas', 3600, function () {
            return MateriaLegislativa::query()
                ->with('cidade')
                ->orderByDesc('acessos')
                ->take(5)
                ->get();
        });

        // Variável de compatibilidade
        $totalMaterias = $totalLeis;

        // IMPORTANTE: Alterado de 'welcome' para 'home.index'
        return view('home.index', compact(
            'totalLeis',
            'totalCidades',
            'totalMaterias',
            'cidadesPrincipais',
            'materiasMaisAcessadas'
        ));
    }

    public function buscar(Request $request)
    {
        $q = trim($request->get('q', ''));
        $page = max(1, (int) $request->get('page', 1));

        if (empty($q)) {
            return redirect()->route('home');
        }

        $buscaService = app(BuscaGlobalService::class);

        $cidades = $this->cidadeRepository->all();
        $searchQuery = Str::slug($q);

        foreach ($cidades as $slug => $cidade) {
            if ($searchQuery === $slug || $searchQuery === Str::slug($cidade['nome'])) {
                return redirect()->route('cidade.home', ['cidade' => $slug]);
            }
        }

        $resultado = $buscaService->buscar($q, $page);

        return view('busca.resultados', [
            'q'          => $q,
            'resultados' => $resultado,
            'cidades'    => $cidades,
        ]);
    }
}