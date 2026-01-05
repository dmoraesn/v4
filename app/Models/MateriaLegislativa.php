<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MateriaLegislativa extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados.
     */
    protected $table = 'materia_legislativa';

    /**
     * Campos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'cidade_id',
        'sapl_id',
        'tipo_sigla',
        'tipo_descricao',
        'numero',
        'ano',
        'data_apresentacao',
        'data_publicacao',
        'ementa',
        'texto_integral',
        'em_tramitacao',
        'regime_tramitacao',
        'indexacao',
        'observacao',
        'acessos',
    ];

    /**
     * Casts de atributos.
     */
    protected $casts = [
        'data_apresentacao' => 'date',
        'data_publicacao'    => 'date',
        'em_tramitacao'     => 'boolean',
    ];

    /**
     * Relação com a cidade à qual a matéria pertence.
     */
    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }

    /**
     * Parlamentares autores da matéria (relação muitos-para-muitos via tabela autoria).
     */
    public function parlamentares(): BelongsToMany
    {
        return $this->belongsToMany(
            Parlamentar::class,
            'autoria',
            'materia_id',
            'parlamentar_id'
        )
        ->withPivot('primeiro_autor')
        ->withTimestamps();
    }

    /**
     * Incrementa o contador de acessos à matéria.
     *
     * @return void
     */
    public function incrementarAcesso(): void
    {
        $this->increment('acessos');
    }
}