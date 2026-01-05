<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SyncCidadesCommand;
use App\Console\Commands\SyncParlamentaresCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Os comandos Artisan fornecidos pela aplicação.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\UpdateHomeStats::class,
        SyncCidadesCommand::class,
        SyncParlamentaresCommand::class,
    ];

    /**
     * Define o agendamento de comandos da aplicação.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Atualização das estatísticas da home a cada 6 horas
        $schedule->command('busca:update-stats')
                 ->everySixHours()
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/update-stats.log'));

        // Sincronização das matérias legislativas e parlamentares de todas as cidades
        // Executa diariamente às 02:00 da manhã (horário do servidor)
        $schedule->command('cidades:sync')
                 ->dailyAt('02:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/sync-cidades.log'));

        // Sincronização exclusiva dos parlamentares de todas as cidades
        // Executa diariamente às 03:00 da manhã (horário do servidor), após as matérias
        $schedule->command('sync:parlamentares')
                 ->dailyAt('03:00')
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/sync-parlamentares.log'));
    }

    /**
     * Registra os comandos da aplicação.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}