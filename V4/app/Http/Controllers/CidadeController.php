<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\SaplService;

class CidadeController extends Controller
{
    /**
     * Dashboard da Cidade
     * Exibe estatísticas básicas e atalhos de busca legislativa.
     * * @param string $cidade O slug da cidade (ex: fortaleza)
     */
    public function show(string $cidade)
    {
        $cidades = $this->getCidades();

        // Verifica se a cidade existe na lista cadastrada
        abort_unless(isset($cidades[$cidade]), 404, 'Cidade não encontrada em nossa base.');

        $cidadeData = $cidades[$cidade];
        
        // Injetando o slug dentro da array de dados para facilitar o uso nas rotas da View
        $cidadeData['slug'] = $cidade;

        /**
         * Estatísticas da cidade (Cache estratégico de 6 horas)
         * Nota: Em produção, estes dados podem vir via SaplService ou consolidação de banco.
         */
        $stats = Cache::remember(
            "cidade:{$cidade}:stats",
            now()->addHours(6),
            function () {
                // Mock de dados para a Etapa 02/03. 
                // Futuramente: Integrar com contagem real via SaplService::totalizadores()
                return [
                    'total_materias' => 42318,
                    'total_leis'     => 18954,
                    'total_autores'  => 43,
                ];
            }
        );

        return view('cidade.home', [
            'cidade' => $cidadeData,
            'stats'  => $stats,
        ]);
    }

    /**
     * Catálogo de Cidades (Fonte de Verdade)
     * Centraliza as configurações de integração SAPL e metadados municipais.
     */
    private function getCidades(): array
    {
        return Cache::rememberForever('cidades', function () {
            return [
                'fortaleza' => [
                    'nome'   => 'Fortaleza',
                    'uf'     => 'CE',
                    'sapl'   => 'https://sapl.fortaleza.ce.leg.br',
                    'brasao' => 'https://upload.wikimedia.org/wikipedia/commons/b/b5/Brasao_fortaleza.png'
                ],
                'sao-goncalo-do-amarante' => [
                    'nome'   => 'São Gonçalo do Amarante',
                    'uf'     => 'CE',
                    'sapl'   => 'https://sapl.saogoncalodoamarante.ce.leg.br',
                    'brasao' => null
                ],
            ];
        });
    }
}