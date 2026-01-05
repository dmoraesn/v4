<?php

namespace App\Jobs;

use App\Models\Cidade;
use App\Models\Parlamentar;
use App\Services\SaplService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncCidadeParlamentaresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Cidade $cidade;

    public function __construct(Cidade $cidade)
    {
        $this->cidade = $cidade;
    }

    /**
     * Sincroniza parlamentares e tenta capturar partido de forma robusta.
     *
     * Estratégia adaptada à realidade das instâncias SAPL:
     * 1. Usa listagem paginada (endpoint /parlamentares/parlamentar/).
     * 2. Tenta partido na listagem.
     * 3. Se não encontrar, tenta no detalhe individual (/parlamentares/parlamentar/{id}/).
     * 4. Se ainda não encontrar, mantém "Sem partido informado" (algumas cidades não expõem).
     */
    public function handle(): void
    {
        Log::info("[PARLAMENTARES] Iniciando sincronização para: {$this->cidade->nome} ({$this->cidade->uf})");

        $sapl = new SaplService($this->cidade->sapl);

        $page = 1;
        $totalProcessados = 0;
        $parlamentaresAtuais = [];

        do {
            $response = $sapl->listarParlamentares($page);

            $results = $response['results'] ?? [];

            if (empty($results)) {
                Log::info("[PARLAMENTARES] Página {$page}: Nenhum parlamentar encontrado.");
                break;
            }

            Log::info("[PARLAMENTARES] Página {$page}: Encontrados " . count($results) . " parlamentares.");

            $upsertData = [];

            foreach ($results as $dados) {
                $saplId = (int) ($dados['id'] ?? 0);

                if ($saplId === 0) {
                    continue;
                }

                $nome = (string) ($dados['nome_parlamentar'] ?? $dados['nome'] ?? 'Desconhecido');

                // 1. Tenta partido na listagem
                $partido = $this->extrairPartido($dados);

                // 2. Se não encontrou na listagem, tenta no detalhe individual
                if (!$partido) {
                    $detalhe = $sapl->parlamentar($saplId); // Método existente no SaplService que faz detalhe com cache
                    $partido = $this->extrairPartido($detalhe);
                }

                $upsertData[] = [
                    'cidade_id'        => $this->cidade->id,
                    'sapl_id'          => $saplId,
                    'nome_parlamentar' => $nome,
                    'partido'          => $partido,
                    'ativo'            => (bool) ($dados['ativo'] ?? true),
                    'fotografia'       => $dados['fotografia'] ?? null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                $parlamentaresAtuais[] = $saplId;
                $totalProcessados++;
            }

            if (!empty($upsertData)) {
                Parlamentar::upsert(
                    $upsertData,
                    ['cidade_id', 'sapl_id'],
                    ['nome_parlamentar', 'partido', 'ativo', 'fotografia', 'updated_at']
                );
            }

            $page++;
        } while ($page <= ($response['pagination']['total_pages'] ?? 1));

        // Marca inativos
        Parlamentar::where('cidade_id', $this->cidade->id)
            ->whereNotIn('sapl_id', $parlamentaresAtuais)
            ->update(['ativo' => false]);

        $totalAtivos = Parlamentar::where('cidade_id', $this->cidade->id)->where('ativo', true)->count();
        $comPartido = Parlamentar::where('cidade_id', $this->cidade->id)
            ->whereNotNull('partido')
            ->where('partido', '!=', 'Sem partido informado')
            ->count();

        Log::info("[PARLAMENTARES] Sincronização concluída para {$this->cidade->nome}:");
        Log::info("   • Processados: {$totalProcessados}");
        Log::info("   • Ativos: {$totalAtivos}");
        Log::info("   • Com partido informado: {$comPartido}");
    }

    /**
     * Extrai sigla do partido de um array de dados SAPL.
     *
     * @param array $dados
     * @return string|null
     */
    protected function extrairPartido(array $dados): ?string
    {
        if (isset($dados['partido']['sigla'])) {
            return $dados['partido']['sigla'];
        }

        if (isset($dados['partido']) && is_string($dados['partido'])) {
            return $dados['partido'];
        }

        return null;
    }
}