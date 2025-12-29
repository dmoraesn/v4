<?php

namespace App\Orchid\Screens;

use App\Models\Cidade;
use App\Repositories\CidadeRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Storage;

class CidadeEditScreen extends Screen
{
    /**
     * @var bool
     */
    protected $exists = false;

    public function __construct(
        protected CidadeRepository $cidadeRepository
    ) {}

    public function query(Cidade $cidade): array
    {
        $this->exists = $cidade->exists;

        return [
            'cidade' => $cidade,
        ];
    }

    public function name(): ?string
    {
        return $this->exists ? 'Editar Cidade' : 'Criar Nova Cidade';
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
                ->canSee($this->exists)
                ->confirm('Deseja excluir esta cidade e todos os seus dados?'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('cidade.nome')
                    ->title('Nome Oficial')
                    ->placeholder('Ex: Fortaleza')
                    ->required(),

                Input::make('cidade.slug')
                    ->title('Slug')
                    ->placeholder('ex-nome-da-cidade')
                    ->help('Identificador único para a URL pública.')
                    ->required(),

                Input::make('cidade.uf')
                    ->title('UF')
                    ->placeholder('CE')
                    ->maxlength(2)
                    ->required(),

                Input::make('cidade.sapl')
                    ->title('URL Base SAPL')
                    ->placeholder('https://sapl.exemplo.ce.leg.br')
                    ->required(),

                Picture::make('cidade.brasao')
                    ->title('Brasão da Cidade')
                    ->storage('public')
                    ->targetRelativeUrl() // Salva o caminho como: 2025/12/29/imagem.png
                    ->acceptedFiles('image/png,image/jpeg,image/webp')
                    ->help('Recomendado: PNG com fundo transparente.'),
            ]),
        ];
    }

    /**
     * Salva ou atualiza a cidade
     */
    public function save(Cidade $cidade, Request $request)
    {
        $data = $request->validate([
            'cidade.slug' => [
                'required',
                Rule::unique('cidades', 'slug')->ignore($cidade->id),
            ],
            'cidade.nome'   => 'required|string|max:255',
            'cidade.uf'     => 'required|string|size:2',
            'cidade.sapl'   => 'required|url',
            'cidade.brasao' => 'nullable|string',
        ])['cidade'];

        // Lógica de limpeza de arquivos antigos
        if ($cidade->exists && $cidade->isDirty('brasao')) {
            $oldBrasao = $cidade->getOriginal('brasao');
            if ($oldBrasao) {
                Storage::disk('public')->delete($oldBrasao);
            }
        }

        $cidade->fill($data)->save();

        $this->cidadeRepository->clearHomeCache();

        Toast::success('Cidade salva com sucesso.');

        return redirect()->route('platform.cidade.list');
    }

    /**
     * Remove a cidade e o arquivo físico do brasão
     */
    public function remove(Cidade $cidade)
    {
        if ($cidade->brasao) {
            Storage::disk('public')->delete($cidade->brasao);
        }

        $cidade->delete();

        $this->cidadeRepository->clearHomeCache();

        Toast::info('Cidade removida com sucesso.');

        return redirect()->route('platform.cidade.list');
    }
}
