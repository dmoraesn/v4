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

    protected $fillable = [
        'slug',
        'nome',
        'uf',
        'sapl',
        'brasao',
        'total_leis',
    ];

    protected $casts = [
        'total_leis' => 'integer',
    ];

    protected $allowedSorts = [
        'slug', 'nome', 'uf', 'total_leis', 'created_at', 'updated_at',
    ];

    /**
     * Accessor que resolve o problema do brasão em qualquer ambiente (Windows/Linux)
     */
    public function getBrasaoUrlAttribute(): ?string
    {
        if (empty($this->brasao)) {
            return null;
        }

        if (filter_var($this->brasao, FILTER_VALIDATE_URL)) {
            return $this->brasao;
        }

        // Limpa o caminho: converte \ em / e remove prefixos de sistema de arquivos
        $cleanPath = str_replace(['\\', 'storage/app/public/', 'storage\app\public\\'], ['/', '', ''], $this->brasao);
        
        return asset('storage/' . ltrim($cleanPath, '/'));
    }

    public function getTotalLeisAttribute(): int
    {
        $cacheKey = "cidade:{$this->id}:total_leis";
        return Cache::remember($cacheKey, now()->addHours(6), function () {
            try {
                if (empty($this->sapl)) return (int) ($this->attributes['total_leis'] ?? 0);
                $sapl = new SaplService($this->sapl);
                $response = $sapl->listarMaterias(1);
                $total = (int) ($response['pagination']['total_entries'] ?? 0);
                $this->updateQuietly(['total_leis' => $total]);
                return $total;
            } catch (\Throwable $e) {
                return (int) ($this->attributes['total_leis'] ?? 0);
            }
        });
    }

    protected static function booted()
    {
        static::saving(function (Cidade $cidade) {
            if (empty($cidade->slug)) {
                $cidade->slug = Str::slug($cidade->nome . '-' . $cidade->uf);
            }
        });
    }
}