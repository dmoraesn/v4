<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
// Importação das classes de filtro do Orchid
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;

class Cidade extends Model
{
    use AsSource, Filterable, Attachable;

    /**
     * Nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'cidades';

    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'nome',
        'slug',
        'uf',
        'sapl',
        'brasao',
    ];

    /**
     * Relacionamento: uma cidade possui muitos parlamentares.
     *
     * @return HasMany
     */
    public function parlamentares(): HasMany
    {
        return $this->hasMany(Parlamentar::class, 'cidade_id');
    }

    /**
     * Colunas permitidas para ordenação padrão.
     *
     * @var array<string>
     */
    protected $allowedSorts = [
        'nome',
        'uf',
        'total_leis_local', // Adicionei aqui pois você usa sort() nessa coluna na Screen
        'created_at',
        'updated_at',
    ];

    /**
     * Colunas permitidas para filtro.
     * Agora mapeando para as classes de filtro corretas.
     *
     * @var array
     */
    protected $allowedFilters = [
        'nome' => Like::class,  // Busca parcial (SQL LIKE %...%)
        'uf'   => Where::class, // Busca exata (SQL =)
        'slug' => Like::class,
    ];
}