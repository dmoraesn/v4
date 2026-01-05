<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Filiacao extends Model
{
    use HasFactory;

    protected $table = 'filiacoes';

    protected $fillable = [
        'cidade_id',
        'parlamentar_id',
        'sapl_id',
        'partido_sigla',
        'partido_nome',
        'data_filiacao',
        'data_desfiliacao',
        'atual',
    ];

    protected $casts = [
        'data_filiacao'    => 'date',
        'data_desfiliacao' => 'date',
        'atual'            => 'boolean',
    ];

    /* =========================
     | Relacionamentos
     ========================= */

    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }

    public function parlamentar(): BelongsTo
    {
        return $this->belongsTo(Parlamentar::class);
    }

    /* =========================
     | Accessors
     ========================= */

    public function getPartidoLabelAttribute(): string
    {
        if (!$this->partido_sigla && !$this->partido_nome) {
            return '-';
        }

        return trim("{$this->partido_sigla} - {$this->partido_nome}");
    }
}
