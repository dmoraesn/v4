<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Parlamentar;

use App\Models\Parlamentar;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ParlamentarByCityListLayout extends Table
{
    /**
     * Fonte de dados para a tabela.
     *
     * @var string
     */
    public $target = 'parlamentares';

    /**
     * Definição das colunas da tabela.
     *
     * @return TD[]
     */
    protected function columns(): array
    {
        return [
            TD::make('nome_parlamentar', 'Nome do Parlamentar')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(fn (Parlamentar $parlamentar) => $parlamentar->nome_parlamentar),

            TD::make('ativo', 'Status')
                ->align(TD::ALIGN_CENTER)
                ->width('140px')
                ->sort()
                ->filter(TD::FILTER_SELECT, [
                    ''  => 'Todos',
                    '1' => 'Ativo',
                    '0' => 'Inativo',
                ])
                ->render(fn (Parlamentar $parlamentar) => view('orchid.partials.status-badge', [
                    'ativo' => $parlamentar->ativo,
                ])),

            TD::make('filiacaoAtual.partido_sigla', 'Partido Atual')
                ->align(TD::ALIGN_CENTER)
                ->width('140px')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(fn (Parlamentar $parlamentar) => view('orchid.partials.partido-badge', [
                    'parlamentar' => $parlamentar,
                ])),

            TD::make(__('Ações'))
                ->align(TD::ALIGN_CENTER)
                ->width('160px')
                ->render(fn (Parlamentar $parlamentar) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make('Editar')
                            ->icon('bs.pencil')
                            ->route('platform.parlamentar.edit', $parlamentar),

                        Button::make($parlamentar->ativo ? 'Desativar' : 'Ativar')
                            ->icon($parlamentar->ativo ? 'bs.toggle-on' : 'bs.toggle-off')
                            ->confirm($parlamentar->ativo
                                ? 'Deseja realmente desativar este parlamentar?'
                                : 'Deseja realmente ativar este parlamentar?')
                            ->method('toggleStatus', ['parlamentar' => $parlamentar->id]),

                        Button::make('Remover')
                            ->icon('bs.trash')
                            ->confirm('Deseja realmente remover este parlamentar? Esta ação é irreversível.')
                            ->method('remove', ['parlamentar' => $parlamentar->id]),
                    ])),
        ];
    }
}