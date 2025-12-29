<?php

namespace App\Repositories;

use App\Models\Cidade;
use Illuminate\Support\Facades\Cache;

class CidadeRepository
{
    /**
     * Retorna o catálogo completo de cidades indexadas.
     */
    public function all(): array
    {
        return Cache::rememberForever('cidades', function () {
            return Cidade::all()
                ->keyBy('slug')
                ->map(fn ($cidade) => [
                    'nome'   => $cidade->nome,
                    'uf'     => $cidade->uf,
                    'sapl'   => $cidade->sapl,
                    'brasao' => $cidade->brasao,
                    'total_leis' => $cidade->total_leis, // Incluído para uso na listagem
                ])
                ->toArray();
        });
    }

    /**
     * Retorna o somatório global de leis de todas as cidades.
     * Cache de longo prazo, invalidado apenas pelo comando de sync.
     */
    public function getTotalGlobalLeis(): int
    {
        return Cache::rememberForever('home:stats.totalMaterias', function () {
            return (int) Cidade::sum('total_leis');
        });
    }

    public function clearCache(): void
    {
        Cache::forget('cidades');
    }

    public function clearHomeCache(): void
    {
        Cache::forget('home:stats.totalMaterias');
        $this->clearCache();
    }
}