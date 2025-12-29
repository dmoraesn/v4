<?php

namespace App\Orchid\Screens;

use App\Models\Cidade;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;

class CidadeListScreen extends Screen
{
    /**
     * Query data.
     */
    public function query(): array
    {
        return [
            'cidades' => Cidade::query()
                ->orderBy('nome')
                ->paginate(),
        ];
    }

    /**
     * Screen title.
     */
    public function name(): ?string
    {
        return 'Cidades Indexadas';
    }

    /**
     * Screen description.
     */
    public function description(): ?string
    {
        return 'Gerencie as cidades com integração SAPL.';
    }

    /**
     * Action buttons.
     */
    public function commandBar(): array
    {
        return [
            Link::make('Adicionar Cidade')
                ->icon('bs.plus-circle')
                ->route('platform.cidade.create'),
        ];
    }

    /**
     * Screen layout.
     */
    public function layout(): array
    {
        return [
            Layout::table('cidades', $this->columns()),
        ];
    }

    /**
     * Table columns.
     */
    protected function columns(): array
    {
        return [
            TD::make('nome', 'Nome')
                ->sort()
                ->filter(),

            TD::make('slug', 'Slug')
                ->sort(),

            TD::make('uf', 'UF')
                ->sort()
                ->width('80px')
                ->alignCenter(),

            TD::make('sapl', 'URL SAPL')
                ->render(fn (Cidade $cidade) => Link::make($cidade->sapl)
                    ->href($cidade->sapl)
                    ->target('_blank')
                ),

            TD::make(__('Actions'))
                ->alignRight()
                ->render(fn (Cidade $cidade) => Link::make('Editar')
                    ->icon('bs.pencil')
                    ->route('platform.cidade.edit', $cidade)
                ),
        ];
    }
}
