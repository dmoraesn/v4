<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Services\SaplService;
use App\Repositories\CidadeRepository;

class MateriaController extends Controller
{
    protected CidadeRepository $cidadeRepository;

    /**
     * Fonte única de verdade para cidades.
     */
    public function __construct(CidadeRepository $cidadeRepository)
    {
        $this->cidadeRepository = $cidadeRepository;
    }

    /**
     * Lista matérias da cidade ou executa busca semântica local.
     */
    public function index(Request $request, string $cidade): View
    {
        /* =====================================================
         * 1️⃣ CONTEXTO DA CIDADE
         * ===================================================== */
        $cidadeData = $this->resolverCidade($cidade);
        $sapl = new SaplService($cidadeData['sapl']);

        /* =====================================================
         * 2️⃣ INPUTS NORMALIZADOS
         * ===================================================== */
        $q    = trim((string) $request->get('q', ''));
        $page = max((int) $request->get('page', 1), 1);

        $isBusca = $q !== '';

        /* =====================================================
         * 3️⃣ EXECUÇÃO PRINCIPAL
         * ===================================================== */
        $resultado = $isBusca
            ? $sapl->buscaSemanticaLocal($q, $page)
            : $sapl->listarMaterias($page);

        /* =====================================================
         * 4️⃣ HIGHLIGHT DE TERMOS
         * ===================================================== */
        if ($isBusca && !empty($resultado['results'])) {
            $termos = $this->extrairTermosRelevantes($q);

            foreach ($resultado['results'] as &$materia) {
                if (!empty($materia['ementa'])) {
                    $materia['ementa_highlight'] = $this->highlightTexto(
                        texto: $materia['ementa'],
                        termos: $termos
                    );
                }
            }
        }

        /* =====================================================
         * 5️⃣ VIEW
         * ===================================================== */
        return view('materia.index', [
            'cidade'     => $cidadeData,
            'materias'   => [
                'dados' => $resultado['results'],
                'total' => $resultado['pagination']['total_entries'] ?? 0,
            ],
            'pagination' => $resultado['pagination'],
            'q'          => $q,
            'isBusca'    => $isBusca,
        ]);
    }

    /**
     * Detalhe da matéria individual.
     */
    public function show(string $cidade, int $id): View
    {
        $cidadeData = $this->resolverCidade($cidade);
        $sapl = new SaplService($cidadeData['sapl']);

        $materia = $sapl->materia($id);
        $autores = $sapl->getAutoresDaMateria($id);

        return view('materia.detalhe', [
            'cidade'  => $cidadeData,
            'materia' => $materia,
            'autores' => $autores,
        ]);
    }

    /* =====================================================
     * CONTEXTO DA CIDADE
     * ===================================================== */

    protected function resolverCidade(string $slug): array
    {
        $cidades = $this->cidadeRepository->all();

        abort_unless(isset($cidades[$slug]), 404);

        return [
            ...$cidades[$slug],
            'slug' => $slug,
        ];
    }

    /* =====================================================
     * HIGHLIGHT / BUSCA
     * ===================================================== */

    protected function extrairTermosRelevantes(string $q): array
    {
        $stopWords = [
            'de','da','do','dos','das',
            'em','para','com','sem',
            'no','na','nos','nas',
            'e','ou','a','o'
        ];

        $tokens = preg_split('/\s+/u', mb_strtolower($q));

        return array_values(array_unique(array_filter($tokens, fn ($t) =>
            mb_strlen($t) > 2 &&
            !in_array($t, $stopWords, true)
        )));
    }

    protected function highlightTexto(string $texto, array $termos): string
    {
        $escaped = e($texto);

        foreach ($termos as $termo) {
            $escaped = preg_replace(
                '/(' . preg_quote($termo, '/') . ')/iu',
                '<mark class="bg-yellow-200 px-1 rounded">$1</mark>',
                $escaped
            );
        }

        return $escaped;
    }
}
