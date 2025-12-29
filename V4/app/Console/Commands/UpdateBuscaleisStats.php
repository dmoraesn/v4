<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class UpdateBuscaleisStats extends Command
{
    protected $signature = 'buscaleis:update-stats';

    protected $description = 'Atualiza estatísticas globais do BuscaLeis a partir dos SAPLs';

    public function handle(): int
    {
        $this->info('🔄 Atualizando estatísticas do BuscaLeis...');

        // ✅ FONTE CONFIÁVEL (não depende de cache)
        $cidades = Config::get('buscaleis.cidades', []);

        if (empty($cidades)) {
            $this->error('❌ Nenhuma cidade configurada');
            return self::FAILURE;
        }

        $totalMaterias = 0;
        $cidadesAtivas = 0;

        foreach ($cidades as $cidade) {
            $baseUrl = rtrim($cidade['sapl'], '/') . '/api';

            $this->line("🔍 Consultando {$cidade['nome']}");

            try {
                $response = Http::timeout(15)
                    ->acceptJson()
                    ->get("{$baseUrl}/materia/materialegislativa/", [
                        'page_size' => 1,
                    ]);

                if (!$response->successful()) {
                    $this->warn("✖ Falha HTTP ({$response->status()})");
                    continue;
                }

                $json = $response->json();
                $total = data_get($json, 'pagination.total_entries');

                if (!is_numeric($total)) {
                    $this->warn("✖ Estrutura inválida do SAPL");
                    continue;
                }

                $totalMaterias += (int) $total;
                $cidadesAtivas++;

                $this->info("✔ {$cidade['nome']}: {$total} matérias");

            } catch (\Throwable $e) {
                $this->error("💥 Erro: {$e->getMessage()}");
            }
        }

        Cache::put('home:stats.totalMaterias', $totalMaterias, now()->addHours(2));
        Cache::put('home:stats.totalCidades', count($cidades), now()->addHours(2));
        Cache::put('home:stats.cidadesAtivas', $cidadesAtivas, now()->addHours(2));
        Cache::put('home:stats.updatedAt', now()->toDateTimeString(), now()->addHours(2));

        $this->info('✅ Estatísticas gravadas no cache');
        $this->info("• Matérias: {$totalMaterias}");
        $this->info("• Cidades ativas: {$cidadesAtivas}");

        return self::SUCCESS;
    }
}
