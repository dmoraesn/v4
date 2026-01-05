<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Cidade;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class ParlamentarDashboardScreen extends Screen
{
    /**
     * Consulta os dados para exibir os cards das cidades com contagem de parlamentares.
     *
     * @return array
     */
    public function query(): array
    {
        $cidades = Cidade::query()
            ->withCount('parlamentares')
            ->orderBy('nome')
            ->get();

        return [
            'cidades' => $cidades,
        ];
    }

    /**
     * Nome exibido no cabeçalho da tela.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Parlamentares';
    }

    /**
     * Descrição exibida abaixo do título.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Selecione uma cidade para visualizar a lista de parlamentares.';
    }

    /**
     * Botões da barra superior.
     *
     * @return array
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Layout da tela: cards com brasão, nome da cidade e quantidade de parlamentares.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            Layout::view('orchid.partials.cidades-cards-parlamentares'),
        ];
    }
}
