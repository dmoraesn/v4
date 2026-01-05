<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Partido;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class PartidoListScreen extends Screen
{
    /**
     * Consulta de dados para a tela.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'partidos' => Partido::query()
                ->orderBy('nome')
                ->paginate(),
        ];
    }

    /**
     * Nome exibido no cabeçalho da tela.
     */
    public function name(): ?string
    {
        return 'Partidos';
    }

    /**
     * Descrição exibida no cabeçalho da tela.
     */
    public function description(): ?string
    {
        return 'Lista de partidos políticos registrados.';
    }

    /**
     * Botões da barra de comandos.
     */
    public function commandBar(): array
    {
        return [
            Link::make('Adicionar Partido')
                ->icon('bs.plus-circle')
                ->route('platform.partido.create'),
        ];
    }

    /**
     * Layout da tela.
     */
    public function layout(): array
    {
        return [
            Layout::table('partidos', [
                TD::make('sigla', 'Sigla')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (Partido $model) => $model->sigla),

                TD::make('nome', 'Nome Completo')
                    ->sort()
                    ->filter(TD::FILTER_TEXT)
                    ->render(fn (Partido $model) => $model->nome),

                TD::make(__('Ações'))
                    ->align(TD::ALIGN_CENTER)
                    ->width('100px')
                    ->render(fn (Partido $model) => Link::make()
                        ->route('platform.partido.edit', $model)
                        ->icon('bs.pencil')),
            ]),
        ];
    }
}
