<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;
use App\Services\SaplService;
use Illuminate\Support\Str;

class Cidade extends Model
{
    use AsSource, Filterable;

    /**
     * Campos atribuíveis em massa.
     */
    protected $fillable = [
        'slug',
        'nome',
        'uf',
        'sapl',
        'brasao',
        'total_leis',
    ];

    /**
     * Casts automáticos.
     */
    protected $casts = [
        'total_leis' => 'integer',
    ];

    /**
     * Colunas permitidas para ordenação no Orchid.
     */
    protected $allowedSorts = [
        'slug',
        'nome',
        'uf',
        'total_leis',
        'created_at',
        'updated_at',
    ];

    /* =====================================================
     * ACCESSORS
     * ===================================================== */

    /**
     * Retorna a URL pública do brasão da cidade.
     * Refatorado para suportar URLs externas e caminhos internos.
     */
    public function getBrasaoUrlAttribute(): ?string
    {
        if (empty($this->brasao)) {
            return null;
        }

        // Se já for uma URL (ex: guardada em array estático anteriormente), retorna direto
        if (filter_var($this->brasao, FILTER_VALIDATE_URL)) {
            return $this->brasao;
        }

        // Caso contrário, busca no disco público do storage
        return Storage::disk('public')->url($this->brasao);
    }

    /**
     * Retorna o total de leis/matérias com cache local (6 horas).
     * Refatorado para evitar recursão infinita e melhorar tratamento de erros.
     */
    public function getTotalLeisAttribute(): int
    {
        $cacheKey = "cidade:{$this->id}:total_leis";

        return Cache::remember($cacheKey, now()->addHours(6), function () {
            try {
                if (empty($this->sapl)) {
                    return (int) ($this->attributes['total_leis'] ?? 0);
                }

                $sapl = new SaplService($this->sapl);
                $response = $sapl->listarMaterias(1);
                $total = (int) ($response['pagination']['total_entries'] ?? 0);

                // Evita que o Accessor dispare um save() comum que poderia causar loops
                $this->updateQuietly(['total_leis' => $total]);

                return $total;
            } catch (\Throwable $e) {
                logger()->warning("Falha ao obter total_leis da cidade {$this->slug}", [
                    'error' => $e->getMessage(),
                    'cidade_id' => $this->id
                ]);

                // Retorna o valor que já estiver salvo no banco como backup
                return (int) ($this->attributes['total_leis'] ?? 0);
            }
        });
    }

    /* =====================================================
     * BOOTSTAGING
     * ===================================================== */

    /**
     * Gera o slug automaticamente ao salvar, se não existir.
     */
    protected static function booted()
    {
        static::saving(function (Cidade $cidade) {
            if (empty($cidade->slug)) {
                $cidade->slug = Str::slug($cidade->nome . '-' . $cidade->uf);
            }
        });
    }
}
