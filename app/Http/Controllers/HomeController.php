<?php

namespace App\Http\Controllers;

use App\Repositories\CidadeRepository;
use App\Services\BuscaGlobalService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    protected CidadeRepository $cidadeRepository;

    /**
     * Injeção do repositório central de cidades.
     * O repositório lida com o cache forever e a agregação de totais legislativos.
     *
     * @param CidadeRepository $cidadeRepository
     */
    public function __construct(CidadeRepository $cidadeRepository)
    {
        $this->cidadeRepository = $cidadeRepository;
    }

    /**
     * Exibe a Home Global com o total real de documentos indexados.
     *
     * @return View
     */
    public function index(): View
    {
        // Recupera o catálogo de cidades (CacheForever no Repository)
        $cidades = $this->cidadeRepository->all();
        
        // Recupera a soma de total_leis de todas as cidades (Cache no Repository)
        $totalLeis = $this->cidadeRepository->getTotalGlobalLeis();

        return view('home.index', [
            'cidades'   => $cidades,
            'totalLeis' => $totalLeis
        ]);
    }

    /**
     * Busca inteligente (Router de Contexto e Busca Global).
     * * Estratégia: 
     * 1. Se o termo bater com o nome de uma cidade cadastrada -> Redireciona para a home da cidade.
     * 2. Caso contrário -> Executa a busca full-text em todos os municípios via BuscaGlobalService.
     *
     * @param Request $request
     * @param BuscaGlobalService $buscaService
     * @return View|RedirectResponse
     */
    public function buscar(Request $request, BuscaGlobalService $buscaService)
    {
        $q = trim((string) $request->get('q', ''));
        $page = max((int) $request->get('page', 1), 1);

        if (empty($q)) {
            return redirect()->route('home');
        }

        // 1. 🔁 Router de Contexto (Verifica se é o nome de uma cidade)
        $cidades = $this->cidadeRepository->all();
        $searchQuery = Str::slug($q);

        foreach ($cidades as $slug => $cidade) {
            // Comparamos o slug da busca com o slug da cidade ou nome normalizado
            if ($searchQuery === $slug || $searchQuery === Str::slug($cidade['nome'])) {
                return redirect()->route('cidade.home', ['cidade' => $slug]);
            }
        }

        // 2. 🔍 BUSCA GLOBAL REAL
        // Se não for um redirecionamento de cidade, processa a busca full-text
        $resultado = $buscaService->buscar($q, $page);

        return view('busca.resultados', [
            'q' => $q,
            'resultados' => $resultado,
            'cidades' => $cidades // Útil se precisar filtrar por cidade na view de resultados
        ]);
    }
}