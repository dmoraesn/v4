<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Parlamentar;

use App\Models\Parlamentar;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ParlamentarListLayout extends Table
{
    /**
     * Fonte de dados.
     * Deve corresponder à chave retornada no Screen::query()
     */
    protected $target = 'parlamentares';

    /**
     * Colunas da tabela.
     */
    protected function columns(): array
    {
        return [

            /* =========================
             | Nome do Parlamentar
             ========================= */
            TD::make('nome_parlamentar', 'Nome do Parlamentar')
                ->sort()
                ->render(fn (Parlamentar $p) =>
                    Link::make($p->nome_parlamentar)
                        ->route('platform.parlamentar.edit', $p)
                ),

            /* =========================
             | Cidade
             ========================= */
            TD::make('cidade', 'Cidade')
                ->sort()
                ->render(function (Parlamentar $p) {
                    if (! $p->cidade) {
                        return '<span class="text-muted">Cidade não informada</span>';
                    }

                    return sprintf(
                        '%s / %s',
                        e($p->cidade->nome),
                        e(strtoupper($p->cidade->uf))
                    );
                })
                ->html(),

            /* =========================
             | Status
             ========================= */
            TD::make('ativo', 'Status')
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->render(fn (Parlamentar $p) =>
                    $p->ativo
                        ? '<span class="badge bg-success">Ativo</span>'
                        : '<span class="badge bg-danger">Inativo</span>'
                )
                ->html(),

            /* =========================
             | Partido Atual (SNAPSHOT)
             ========================= */
            TD::make('partido_label', 'Partido Atual')
                ->render(fn (Parlamentar $p) =>
                    e($p->partido_label)
                ),

            /* =========================
             | Ações
             ========================= */
            TD::make(__('Ações'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Parlamentar $p) =>
                    DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Editar'))
                                ->route('platform.parlamentar.edit', $p)
                                ->icon('bs.pencil'),

                            Button::make(__('Remover'))
                                ->icon('bs.trash')
                                ->confirm(__('Tem certeza que deseja remover este parlamentar?'))
                                ->method('remove', [
                                    'id' => $p->id,
                                ]),
                        ])
                ),
        ];
    }
}
