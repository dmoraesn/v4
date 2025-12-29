<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use Illuminate\Support\Facades\Cache;
use App\Services\SaplService;

class Cidade extends Model
{
    use AsSource, Filterable;

    protected $fillable = [
        'slug',
        'nome',
        'uf',
        'sapl',
        'brasao',
        'total_leis', // Novo campo para cache local
    ];

    protected $casts = [
        'total_leis' => 'integer',
    ];

    protected $allowedSorts = [
        'slug',
        'nome',
        'uf',
        'total_leis',
        'created_at',
        'updated_at',
    ];

    /**
     * Acessor para obter o total de leis com cache por cidade (TTL 6 horas).
     */
    public function getTotalLeisAttribute(): int
    {
        $cacheKey = "cidade:{$this->id}:total_leis";

        return Cache::remember($cacheKey, now()->addHours(6), function () {
            try {
                $sapl = new SaplService($this->sapl . '/api');

                $response = $sapl->listarMaterias(1);

                $total = $response['pagination']['total_entries'] ?? 0;

                // Atualiza o campo no banco para consulta rápida (opcional)
                $this->updateQuietly(['total_leis' => $total]);

                return $total;
            } catch (\Throwable $e) {
                \Log::warning("Falha ao atualizar total_leis para cidade {$this->slug}: " . $e->getMessage());

                return $this->attributes['total_leis'] ?? 0;
            }
        });
    }

    /**
     * Atualiza o total de leis imediatamente (útil em comandos ou após alterações).
     */
    public function atualizarTotalLeis(): int
    {
        try {
            $sapl = new SaplService($this->sapl . '/api');

            $response = $sapl->listarMaterias(1);

            $total = $response['pagination']['total_entries'] ?? 0;

            $this->update(['total_leis' => $total]);

            Cache::forget("cidade:{$this->id}:total_leis");

            return $total;
        } catch (\Throwable $e) {
            \Log::warning("Falha ao atualizar total_leis para cidade {$this->slug}: " . $e->getMessage());

            return $this->total_leis ?? 0;
        }
    }
}