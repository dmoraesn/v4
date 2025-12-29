<?php

namespace App\Orchid\Screens;

use App\Models\Cidade;
use App\Repositories\CidadeRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CidadeEditScreen extends Screen
{
    public function __construct(
        protected CidadeRepository $cidadeRepository
    ) {}

    /**
     * GET: cria ou edita
     */
    public function query(Cidade $cidade): array
    {
        return [
            'cidade' => $cidade,
        ];
    }

    public function name(): ?string
    {
        return request()->routeIs('platform.cidade.edit')
            ? 'Editar Cidade'
            : 'Criar Nova Cidade';
    }

    public function commandBar(): array
    {
        return [
            Button::make('Salvar Cidade')
                ->icon('bs.check-circle')
                ->method('save'),

            Button::make('Remover')
                ->icon('bs.trash')
                ->method('remove')
                ->canSee(request()->routeIs('platform.cidade.edit'))
                ->confirm('Deseja excluir esta cidade? Esta ação é irreversível.'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows($this->fields()),
        ];
    }

    protected function fields(): array
    {
        return [
            Input::make('cidade.slug')
                ->title('Slug')
                ->required(),

            Input::make('cidade.nome')
                ->title('Nome Oficial')
                ->required(),

            Input::make('cidade.uf')
                ->title('UF')
                ->maxlength(2)
                ->required(),

            Input::make('cidade.sapl')
                ->title('URL Base SAPL')
                ->required(),

            Input::make('cidade.brasao')
                ->title('URL do Brasão'),
        ];
    }

    /**
     * POST: cria ou atualiza
     */
    public function save(Request $request)
    {
        // 🔑 Reconstrói o model corretamente
        $cidade = request()->routeIs('platform.cidade.edit')
            ? Cidade::findOrFail($request->route('cidade'))
            : new Cidade();

        $data = $request->validate([
            'cidade.slug' => [
                'required',
                Rule::unique('cidades', 'slug')->ignore($cidade->id),
            ],
            'cidade.nome'   => 'required|string|max:255',
            'cidade.uf'     => 'required|string|size:2',
            'cidade.sapl'   => 'required|url',
            'cidade.brasao' => 'nullable|url',
        ])['cidade'];

        $cidade->fill($data)->save();

        $this->cidadeRepository->clearHomeCache();

        Toast::success('Cidade salva com sucesso.');

        return redirect()->route('platform.cidade.list');
    }

    /**
     * Exclusão (somente edit)
     */
    public function remove(Request $request)
    {
        $cidade = Cidade::findOrFail($request->route('cidade'));

        $cidade->delete();

        $this->cidadeRepository->clearHomeCache();

        Toast::info('Cidade removida com sucesso.');

        return redirect()->route('platform.cidade.list');
    }
}
