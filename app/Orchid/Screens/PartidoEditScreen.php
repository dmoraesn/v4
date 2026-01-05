<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Partido;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartidoEditScreen extends Screen
{
    public $partido;

    /**
     * Consulta de dados para a tela.
     *
     * @param Partido $partido
     * @return array
     */
    public function query(Partido $partido): array
    {
        return [
            'partido' => $partido,
        ];
    }

    /**
     * Nome exibido no cabeçalho da tela.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->partido->exists ? 'Editar Partido' : 'Criar Partido';
    }

    /**
     * Botões da barra de comandos.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Salvar')
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make('Remover')
                ->icon('bs.trash')
                ->method('remove')
                ->canSee($this->partido->exists),
        ];
    }

    /**
     * Layout da tela.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('partido.sapl_id')
                    ->title('ID no SAPL')
                    ->type('number')
                    ->placeholder('Ex: 13')
                    ->help('Identificador único do partido no sistema SAPL. Deixe vazio se não houver.'),

                Input::make('partido.sigla')
                    ->title('Sigla')
                    ->required()
                    ->maxlength(10)
                    ->placeholder('Ex: PT'),

                Input::make('partido.nome')
                    ->title('Nome Completo')
                    ->required()
                    ->placeholder('Ex: Partido dos Trabalhadores'),
            ]),
        ];
    }

    /**
     * Salva ou atualiza o partido.
     *
     * @param Partido $partido
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Partido $partido, Request $request)
    {
        $dados = $request->validate([
            'partido.sapl_id' => 'nullable|integer',
            'partido.sigla'   => [
                'required',
                'max:10',
                Rule::unique('partidos', 'sigla')->ignore($partido->id),
            ],
            'partido.nome'    => 'required|string|max:255',
        ])['partido'];

        // Converte campo sapl_id vazio para null
        $dados['sapl_id'] = $dados['sapl_id'] ?: null;

        try {
            $partido->fill($dados)->save();

            Toast::info('Partido salvo com sucesso.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                Toast::error('Já existe um partido com esta sigla.');
                return back()->withInput();
            }

            Toast::error('Erro ao salvar o partido.');
        }

        return redirect()->route('platform.partido.list');
    }

    /**
     * Remove o partido.
     *
     * @param Partido $partido
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Partido $partido)
    {
        try {
            $partido->delete();
            Toast::info('Partido removido com sucesso.');
        } catch (\Exception $e) {
            Toast::error('Erro ao remover o partido.');
        }

        return redirect()->route('platform.partido.list');
    }
}