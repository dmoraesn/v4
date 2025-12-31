<?php

namespace App\Orchid\Screens;

use App\Models\Cidade;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Toast;

class CidadeListScreen extends Screen
{
    /**
     * Busca os dados das cidades.
     * @return array
     */
    public function query(): array
    {
        return [
            // Paginação de 15 itens para manter a fluidez
            'cidades' => Cidade::filters()
                ->defaultSort('nome', 'asc')
                ->paginate(15),
        ];
    }

    /**
     * Nome da tela.
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Cidades Indexadas';
    }

    /**
     * Descrição da tela.
     * @return string|null
     */
    public function description(): ?string
    {
        return 'Gerenciamento de municípios e integração SAPL.';
    }

    /**
     * Botões de ação globais.
     * @return array
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
     * Layout da tabela simples (Sem brasão).
     * @return array
     */
    public function layout(): array
    {
        return [
            Layout::table('cidades', [
                TD::make('nome', 'Cidade')
                    ->sort()
                    ->render(fn (Cidade $cidade) => "<strong>{$cidade->nome}</strong>"),

                TD::make('uf', 'UF')
                    ->sort()
                    ->align(TD::ALIGN_CENTER),

                TD::make('total_leis', 'Matérias')
                    ->sort()
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn (Cidade $cidade) => number_format($cidade->total_leis, 0, ',', '.')),

                TD::make('sapl', 'Link SAPL')
                    ->render(fn (Cidade $cidade) => Link::make('Acessar')
                        ->href($cidade->sapl)
                        ->target('_blank')
                        ->icon('bs.box-arrow-up-right')
                        ->class('btn btn-link text-primary p-0')),

                TD::make('Ações')
                    ->align(TD::ALIGN_RIGHT)
                    ->render(fn (Cidade $cidade) => DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make('Editar')
                                ->route('platform.cidade.edit', $cidade)
                                ->icon('bs.pencil'),

                            Button::make('Remover')
                                ->icon('bs.trash')
                                ->confirm("Deseja realmente excluir a cidade {$cidade->nome}?")
                                ->method('remove', [
                                    'id' => $cidade->id,
                                ]),
                        ])),
            ]),
        ];
    }

    /**
     * Método para remover a cidade diretamente da listagem.
     * @param int $id
     */
    public function remove(int $id): void
    {
        $cidade = Cidade::findOrFail($id);
        $cidade->delete();

        Toast::info('Cidade removida com sucesso.');
    }
}