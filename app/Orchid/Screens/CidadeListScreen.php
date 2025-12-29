<?php

namespace App\Orchid\Screens;

use App\Models\Cidade;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class CidadeListScreen extends Screen
{
    /**
     * Busca os dados das cidades.
     */
    public function query(): array
    {
        return [
            'cidades' => Cidade::query()
                ->orderBy('nome')
                ->paginate(12),
        ];
    }

    public function name(): ?string
    {
        return 'Cidades Indexadas';
    }

    public function description(): ?string
    {
        return 'Gerencie as cidades com integração SAPL.';
    }

    public function commandBar(): array
    {
        return [
            Link::make('Adicionar Cidade')
                ->icon('bs.plus-circle')
                ->route('platform.cidade.create'),
        ];
    }

    public function layout(): array
    {
        return [
            // Renderiza o grid de cards usando o partial refatorado
            Layout::view('orchid.partials.cidades-cards'),
        ];
    }
}
