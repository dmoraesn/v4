<?php

namespace App\Jobs;

use App\Models\Cidade;
use App\Models\MateriaLegislativa;
use App\Models\Parlamentar;
use App\Services\SaplService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SyncCidadeMateriasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Cidade $cidade;

    public function __construct(Cidade $cidade)
    {
        $this->cidade = $cidade;
    }

    public function handle(): void
    {
        // 🚀 Performance
        Model::unsetEventDispatcher();
        DB::statement("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");

        $sapl = new SaplService($this->cidade->sapl);

        $page = 1;
        $total = 0;

        // Cache de parlamentares (sapl_id => id)
        $parlamentaresCache = [];

        // 🔍 Detecta se a tabela pivot existe
        $pivotTable = 'materia_legislativa_parlamentar';
        $pivotExiste = Schema::hasTable($pivotTable);

        if (!$pivotExiste) {
            Log::warning("Tabela pivot '{$pivotTable}' não existe. Autores serão ignorados.");
        }

        do {
            $response = $sapl->listarMaterias($page);
            $materias = $response['results'] ?? [];

            if (empty($materias)) {
                break;
            }

            $materiasBatch = [];
            $pivotBatch = [];

            foreach ($materias as $dados) {
                // 🛡️ NORMALIZAÇÃO TOTAL
                $tipo = is_array($dados['tipo'] ?? null) ? $dados['tipo'] : [];
                $regime = is_array($dados['regime_tramitacao'] ?? null) ? $dados['regime_tramitacao'] : [];

                $materiasBatch[] = [
                    'cidade_id'           => $this->cidade->id,
                    'sapl_id'             => (int) $dados['id'],
                    'tipo_sigla'          => (string) ($tipo['sigla'] ?? ''),
                    'tipo_descricao'      => (string) ($tipo['descricao'] ?? ''),
                    'numero'              => (int) ($dados['numero'] ?? 0),
                    'ano'                 => (int) ($dados['ano'] ?? 0),
                    'data_apresentacao'   => $dados['data_apresentacao'] ?? null,
                    'data_publicacao'     => $dados['data_publicacao'] ?? null,
                    'ementa'              => (string) ($dados['ementa'] ?? ''),
                    'texto_integral'      => $dados['texto_integral'] ?? null,
                    'em_tramitacao'       => (bool) ($dados['em_tramitacao'] ?? true),
                    'regime_tramitacao'   => (string) ($regime['descricao'] ?? ''),
                    'indexacao'           => (string) ($dados['indexacao'] ?? ''),
                    'observacao'          => (string) ($dados['observacao'] ?? ''),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }

            // 🚀 UPSERT EM MASSA
            MateriaLegislativa::upsert(
                $materiasBatch,
                ['cidade_id', 'sapl_id'],
                [
                    'tipo_sigla',
                    'tipo_descricao',
                    'numero',
                    'ano',
                    'data_apresentacao',
                    'data_publicacao',
                    'ementa',
                    'texto_integral',
                    'em_tramitacao',
                    'regime_tramitacao',
                    'indexacao',
                    'observacao',
                    'updated_at',
                ]
            );

            // 🔗 AUTORIA (SÓ SE A TABELA EXISTIR)
            if ($pivotExiste) {
                $materiasIds = MateriaLegislativa::where('cidade_id', $this->cidade->id)
                    ->whereIn('sapl_id', array_column($materias, 'id'))
                    ->pluck('id', 'sapl_id');

                foreach ($materias as $dados) {
                    $materiaId = $materiasIds[$dados['id']] ?? null;
                    if (!$materiaId) {
                        continue;
                    }

                    $autores = $sapl->getAutoresDaMateria($dados['id']);

                    foreach ($autores as $index => $autor) {
                        $saplAutorId = (int) ($autor['id'] ?? 0);
                        if (!$saplAutorId) {
                            continue;
                        }

                        if (!isset($parlamentaresCache[$saplAutorId])) {
                            $parlamentar = Parlamentar::updateOrCreate(
                                [
                                    'cidade_id' => $this->cidade->id,
                                    'sapl_id'   => $saplAutorId,
                                ],
                                [
                                    'nome_parlamentar' => (string) ($autor['nome'] ?? 'Desconhecido'),
                                    'ativo'            => true,
                                    'fotografia'       => $autor['fotografia'] ?? null,
                                ]
                            );

                            $parlamentaresCache[$saplAutorId] = $parlamentar->id;
                        }

                        $pivotBatch[] = [
                            'materia_legislativa_id' => $materiaId,
                            'parlamentar_id'         => $parlamentaresCache[$saplAutorId],
                            'primeiro_autor'         => $index === 0,
                        ];
                    }
                }

                if (!empty($pivotBatch)) {
                    DB::table($pivotTable)->upsert(
                        $pivotBatch,
                        ['materia_legislativa_id', 'parlamentar_id'],
                        ['primeiro_autor']
                    );
                }
            }

            $total += count($materias);
            $page++;

            if ($page % 5 === 0) {
                Log::info("{$this->cidade->nome}: {$total} matérias sincronizadas");
            }

        } while ($page <= ($response['pagination']['total_pages'] ?? 1));

        // 📌 Finaliza
        $this->cidade->update([
            'total_leis_local' => $total,
            'last_sync_at'     => Carbon::now(),
        ]);
    }
}
