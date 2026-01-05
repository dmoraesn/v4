<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cidade;
use App\Jobs\SyncCidadeMateriasJob;

class SyncCidadesCommand extends Command
{
    protected $signature = 'sync:cidades {cidade_id?}';
    protected $description = 'Sincroniza matérias legislativas das cidades via SAPL';

    public function handle(): int
    {
        $cidadeId = $this->argument('cidade_id');

        if ($cidadeId) {
            $cidade = Cidade::find($cidadeId);

            if (!$cidade) {
                $this->error("Cidade ID {$cidadeId} não encontrada.");
                return Command::FAILURE;
            }

            if (empty($cidade->sapl)) {
                $this->error("Cidade {$cidade->nome} não possui SAPL configurado.");
                return Command::FAILURE;
            }

            SyncCidadeMateriasJob::dispatch($cidade);
            $this->info("Job disparado para cidade: {$cidade->nome}");

            return Command::SUCCESS;
        }

        // 🔥 Sem filtro cego por sapl
        $cidades = Cidade::all();

        if ($cidades->isEmpty()) {
            $this->warn('Nenhuma cidade cadastrada.');
            return Command::SUCCESS;
        }

        $disparados = 0;

        foreach ($cidades as $cidade) {
            if (empty($cidade->sapl)) {
                $this->warn("Pulando {$cidade->nome} (SAPL não configurado)");
                continue;
            }

            SyncCidadeMateriasJob::dispatch($cidade);
            $this->info("Job disparado para cidade: {$cidade->nome}");
            $disparados++;
        }

        if ($disparados === 0) {
            $this->warn('Nenhuma cidade elegível para sincronização.');
        }

        return Command::SUCCESS;
    }
}
