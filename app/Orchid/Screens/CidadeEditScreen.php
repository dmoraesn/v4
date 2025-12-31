<?php

namespace App\Orchid\Screens;

use App\Models\Cidade;
use App\Repositories\CidadeRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\Sight;

class CidadeEditScreen extends Screen
{
    /**
     * @var bool
     */
    public $exists = false;

    /**
     * @param CidadeRepository $cidadeRepository
     */
    public function __construct(protected CidadeRepository $cidadeRepository)
    {
    }

    /**
     * @param Cidade $cidade
     * @return array
     */
    public function query(Cidade $cidade): array
    {
        $this->exists = $cidade->exists;

        return [
            'cidade' => $cidade,
        ];
    }

    /**
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->exists ? 'Editar Cidade' : 'Criar Nova Cidade';
    }

    /**
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Voltar')
                ->icon('bs.arrow-left')
                ->route('platform.cidade.list'),

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

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            // 1. Injetamos um CSS para esconder o preview gigante que o Orchid cria automaticamente
            Layout::view('orchid.partials.custom-css'),

            // 2. Nossa Miniatura Controlada (Legend)
            Layout::legend('cidade', [
                Sight::make('brasao', 'Miniatura Atual')
                    ->render(function (Cidade $cidade) {
                        if (empty($cidade->brasao)) {
                            return "<div class='p-3 border rounded bg-light text-center' style='width: 100px;'>
                                        <span class='text-muted fw-bold'>SEM IMAGEM</span>
                                    </div>";
                        }

                        // Lógica de URL robusta
                        $path = str_replace(['\\', 'storage/app/public/', 'storage\app\public\\'], ['/', '', ''], $cidade->brasao);
                        $url = asset('storage/' . ltrim($path, '/'));

                        return "<img src='{$url}' 
                                     alt='Preview' 
                                     class='img-fluid rounded border shadow-sm p-1' 
                                     style='max-height: 120px; background: #fff;'
                                     onerror=\"this.src='https://ui-avatars.com/api/?name=".urlencode($cidade->nome)."&background=f3f4f6&color=6b7280'\">";
                    }),
            ])->title('Visualização do Brasão')
              ->canSee($this->exists),

            // 3. Campos de Edição
            Layout::rows([
                Input::make('cidade.nome')
                    ->title('Nome Oficial')
                    ->required(),

                Input::make('cidade.slug')
                    ->title('Slug')
                    ->required(),

                Input::make('cidade.uf')
                    ->title('UF')
                    ->required()
                    ->maxlength(2),

                Input::make('cidade.sapl')
                    ->title('URL Base SAPL')
                    ->required(),

                Picture::make('cidade.brasao')
                    ->title('Upload do Brasão')
                    ->storage('public')
                    ->targetRelativeUrl()
                    ->acceptedFiles('image/png,image/jpeg,image/webp')
                    ->help('O preview gigante abaixo foi ocultado para melhorar a navegação.'),
            ]),
        ];
    }

    public function save(Cidade $cidade, Request $request)
    {
        $validated = $request->validate([
            'cidade.slug'   => ['required', Rule::unique('cidades', 'slug')->ignore($cidade->id)],
            'cidade.nome'   => 'required|string|max:255',
            'cidade.uf'     => 'required|string|size:2',
            'cidade.sapl'   => 'required|url',
            'cidade.brasao' => 'nullable|string',
        ]);

        $data = $validated['cidade'];

        if ($cidade->exists && $cidade->isDirty('brasao')) {
            $old = $cidade->getOriginal('brasao');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
        }

        $cidade->fill($data)->save();
        $this->cidadeRepository->clearHomeCache();

        Toast::success('Cidade salva com sucesso.');
        return redirect()->route('platform.cidade.list');
    }

    public function remove(Cidade $cidade)
    {
        if ($cidade->brasao) {
            Storage::disk('public')->delete($cidade->brasao);
        }

        $cidade->delete();
        $this->cidadeRepository->clearHomeCache();

        Toast::info('Cidade removida.');
        return redirect()->route('platform.cidade.list');
    }
}