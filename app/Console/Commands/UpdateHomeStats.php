<?php

namespace App\Console\Commands;

use App\Models\Cidade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class UpdateHomeStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'busca:update-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza as estatísticas da página inicial (total de matérias legislativas) usando os valores armazenados no banco';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $total = Cidade::sum('total_leis');

        Cache::put('home:stats.totalMaterias', $total, now()->addHours(6));

        $this->info("Estatísticas atualizadas com sucesso: totalMaterias = {$total}");
    }
}