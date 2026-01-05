<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\HttpFilter;

class Parlamentar extends Model
{
    use HasFactory, AsSource, Filterable;

    protected $table = 'parlamentar';

    protected $fillable = [
        'cidade_id',
        'sapl_id',
        'nome_parlamentar',
        'ativo',
        'fotografia',
        'acessos',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Colunas permitidas para ordenação automática no Orchid.
     */
    protected $allowedSorts = [
        'nome_parlamentar',
        'cidade_id',
        'ativo',
        'acessos',
    ];

    /**
     * Relacionamentos
     */
    public function cidade(): BelongsTo
    {
        return $this->belongsTo(Cidade::class);
    }

    public function filiacaoAtual(): HasOne
    {
        return $this->hasOne(Filiacao::class)->where('atual', true);
    }

    /**
     * Matérias legislativas das quais o parlamentar é autor.
     */
    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(
            MateriaLegislativa::class,
            'autoria',
            'parlamentar_id',
            'materia_id'
        )
        ->withPivot('primeiro_autor')
        ->withTimestamps();
    }

    /**
     * Incrementa o contador de acessos ao perfil do parlamentar.
     *
     * @return void
     */
    public function incrementarAcesso(): void
    {
        $this->increment('acessos');
    }

    /**
     * Accessor para exibir partido atual formatado.
     */
    public function getPartidoAtualAttribute(): string
    {
        if (! $this->filiacaoAtual) {
            return 'Sem filiação ativa';
        }

        return trim(
            "{$this->filiacaoAtual->partido_sigla} - {$this->filiacaoAtual->partido_nome}"
        );
    }

    /**
     * Filtros Orchid para listagem no painel.
     */
    public function filters(): array
    {
        return [
            'nome_parlamentar' => Like::class,
            'ativo' => Where::class,
            HttpFilter::make('cidade_id')
                ->title('Cidade')
                ->quickSearch()
                ->options(
                    Cidade::query()
                        ->orderBy('nome')
                        ->pluck('nome', 'id')
                        ->prepend('Todas as cidades', '')
                        ->toArray()
                )
                ->default(null)
                ->placeholder('Todas as cidades'),
        ];
    }
}