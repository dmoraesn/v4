<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partido extends Model
{
    use HasFactory;

    protected $table = 'partidos';

    protected $fillable = [
        'sapl_id',
        'sigla',
        'nome',
    ];

    /**
     * Filiações relacionadas a este partido.
     */
    public function filiacoes(): HasMany
    {
        return $this->hasMany(Filiacao::class);
    }

    /**
     * Parlamentares atualmente filiados a este partido (via filiação atual).
     */
    public function parlamentaresAtuais()
    {
        return $this->hasManyThrough(
            Parlamentar::class,
            Filiacao::class,
            'partido_id',
            'id',
            'id',
            'parlamentar_id'
        )->where('filiacoes.atual', true);
    }
}