<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cidade;
use App\Jobs\SyncCidadeParlamentaresJob;

class SyncParlamentaresCommand extends Command
{
    /**
     * Nome e assinatura do comando Artisan.
     *
     * @var string
     */
    protected $signature = 'sync:parlamentares {cidade_id? : ID da cidade (opcional – sincroniza todas se omitido)}';

    /**
     * Descrição do comando.
     *
     * @var string
     */
    protected $description = 'Sincroniza apenas a lista de parlamentares de uma ou todas as cidades a partir do SAPL com contagem visual detalhada';

    /**
     * Executa o comando.
     *
     * @return int
     */
    public function handle(): int
    {
        $cidadeId = $this->argument('cidade_id');

        if ($cidadeId) {
            $cidade = Cidade::find($cidadeId);

            if (!$cidade) {
                $this->error("Cidade com ID {$cidadeId} não encontrada.");
                return self::FAILURE;
            }

            if (empty($cidade->sapl)) {
                $this->error("Cidade {$cidade->nome} não possui SAPL configurado.");
                return self::FAILURE;
            }

            $this->info("Sincronizando parlamentares para: {$cidade->nome} ({$cidade->uf})");
            SyncCidadeParlamentaresJob::dispatchSync($cidade);

            $totalAtivos = \App\Models\Parlamentar::where('cidade_id', $cidade->id)->where('ativo', true)->count();
            $totalRegistros = \App\Models\Parlamentar::where('cidade_id', $cidade->id)->count();

            $this->newLine();
            $this->info("Resumo para {$cidade->nome}:");
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Parlamentares ativos', $totalAtivos],
                    ['Total de registros', $totalRegistros],
                ]
            );

            return self::SUCCESS;
        }

        $cidades = Cidade::whereNotNull('sapl')->get();

        if ($cidades->isEmpty()) {
            $this->warn('Nenhuma cidade com SAPL configurado encontrada.');
            return self::SUCCESS;
        }

        $this->info("Iniciando sincronização de parlamentares para {$cidades->count()} cidades...");

        $bar = $this->output->createProgressBar($cidades->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->setMessage('Processando...');
        $bar->start();

        foreach ($cidades as $cidade) {
            $bar->setMessage("{$cidade->nome}");
            SyncCidadeParlamentaresJob::dispatchSync($cidade);
            $bar->advance();
        }

        $bar->setMessage('Concluído');
        $bar->finish();

        $this->newLine(2);
        $this->info('Resumo geral por cidade:');

        $resumo = $cidades->map(function ($cidade) {
            $ativos = \App\Models\Parlamentar::where('cidade_id', $cidade->id)->where('ativo', true)->count();
            $total = \App\Models\Parlamentar::where('cidade_id', $cidade->id)->count();
            return [
                'cidade' => "{$cidade->nome} ({$cidade->uf})",
                'ativos' => $ativos,
                'total'  => $total,
            ];
        })->sortByDesc('ativos');

        $this->table(
            ['Cidade', 'Ativos', 'Total'],
            $resumo->values()->toArray()
        );

        return self::SUCCESS;
    }
}