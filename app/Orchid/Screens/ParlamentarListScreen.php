<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Parlamentar;
use App\Orchid\Layouts\Parlamentar\ParlamentarListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ParlamentarListScreen extends Screen
{
    /**
     * Query data.
     *
     * Retorna os dados necessários para a tela de listagem.
     * Utiliza eager loading apenas de relacionamentos EXISTENTES.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'parlamentares' => Parlamentar::query()
                ->with([
                    'cidade',
                    'filiacaoAtual', // ✅ relacionamento real
                ])
                ->filters() // filtros definidos no Model (Filterable)
                ->defaultSort('nome_parlamentar', 'asc')
                ->paginate(),
        ];
    }

    /**
     * Nome exibido no cabeçalho da tela.
     */
    public function name(): ?string
    {
        return 'Parlamentares';
    }

    /**
     * Descrição exibida abaixo do título.
     */
    public function description(): ?string
    {
        return 'Lista completa de parlamentares indexados em todas as cidades.';
    }

    /**
     * Botões de ação do topo da tela.
     *
     * @return array
     */
    public function commandBar(): array
    {
        return [
            Link::make('Adicionar Parlamentar')
                ->icon('bs.plus-circle')
                ->route('platform.parlamentar.create'),
        ];
    }

    /**
     * Layouts utilizados na tela.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            ParlamentarListLayout::class,
        ];
    }

    /**
     * Método opcional para ações futuras (ex: remoção em massa).
     */
    public function remove(Request $request): void
    {
        // Implementar se necessário
    }
}
