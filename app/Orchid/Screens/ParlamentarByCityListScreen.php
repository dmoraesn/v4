<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Cidade;
use App\Models\Parlamentar;
use App\Orchid\Layouts\Parlamentar\ParlamentarByCityListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ParlamentarByCityListScreen extends Screen
{
    public ?Cidade $cidade = null;

    /**
     * Carrega a cidade e os parlamentares pertencentes a ela.
     */
    public function query(Cidade $cidade): array
    {
        $this->cidade = $cidade;

        $parlamentares = Parlamentar::query()
            ->with(['filiacaoAtual'])
            ->where('cidade_id', $cidade->id)
            ->filters()
            ->defaultSort('nome_parlamentar', 'asc')
            ->paginate();

        return [
            'parlamentares' => $parlamentares,
            'cidade'        => $cidade,
        ];
    }

    /**
     * Nome da tela.
     */
    public function name(): ?string
    {
        return 'Parlamentares - ' . $this->cidade?->nome . ' / ' . $this->cidade?->uf;
    }

    /**
     * Descrição.
     */
    public function description(): ?string
    {
        return 'Lista de parlamentares da cidade selecionada.';
    }

    /**
     * Botões superiores.
     */
    public function commandBar(): array
    {
        return [
            Link::make('← Voltar ao Dashboard')
                ->route('platform.parlamentar.dashboard')
                ->icon('bs.arrow-left'),

            Link::make('Adicionar Parlamentar')
                ->icon('bs.plus-circle')
                ->route('platform.parlamentar.create'),
        ];
    }

    /**
     * Layout da listagem.
     */
    public function layout(): array
    {
        return [
            ParlamentarByCityListLayout::class,
        ];
    }

    /**
     * Alterna o status (ativo/inativo) do parlamentar.
     *
     * @param Request $request
     * @return void
     */
    public function toggleStatus(Request $request): void
    {
        $parlamentar = Parlamentar::findOrFail($request->get('parlamentar'));

        $parlamentar->ativo = !$parlamentar->ativo;
        $parlamentar->save();

        Alert::success('Status do parlamentar alterado com sucesso.');
    }

    /**
     * Remove o parlamentar permanentemente.
     *
     * @param Request $request
     * @return void
     */
    public function remove(Request $request): void
    {
        $parlamentar = Parlamentar::findOrFail($request->get('parlamentar'));

        $parlamentar->delete();

        Alert::success('Parlamentar removido com sucesso.');
    }
}