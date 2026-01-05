<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Cidade;
use App\Models\Filiacao;
use App\Models\Parlamentar;
use App\Models\Partido;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ParlamentarEditScreen extends Screen
{
    /**
     * Modelo do parlamentar (necessário ser nullable para o Orchid).
     */
    public ?Parlamentar $parlamentar = null;

    /**
     * Carrega os dados iniciais para o formulário.
     *
     * @param Parlamentar $parlamentar
     * @return array
     */
    public function query(Parlamentar $parlamentar): array
    {
        $this->parlamentar = $parlamentar;

        // Preenche o ID do partido atual para exibição no select
        $partidoAtualId = null;
        if ($parlamentar->exists && $parlamentar->filiacaoAtual) {
            $partidoAtualId = Partido::where('sigla', $parlamentar->filiacaoAtual->partido_sigla)
                ->value('id');
        }

        return [
            'parlamentar'       => $parlamentar,
            'partido_atual_id'  => $partidoAtualId,
        ];
    }

    /**
     * Nome exibido no cabeçalho da tela.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->parlamentar?->exists
            ? 'Editar Parlamentar'
            : 'Criar Parlamentar';
    }

    /**
     * Botões da barra superior.
     *
     * @return array
     */
    public function commandBar(): array
    {
        return [
            Button::make('Salvar')
                ->icon('bs.check-circle')
                ->method('save'),
        ];
    }

    /**
     * Layout do formulário.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                // ID interno (apenas visualização)
                Input::make('parlamentar.id')
                    ->title('ID')
                    ->readonly()
                    ->canSee($this->parlamentar?->exists ?? false),

                // ID SAPL (apenas visualização)
                Input::make('parlamentar.sapl_id')
                    ->title('ID SAPL')
                    ->readonly()
                    ->canSee($this->parlamentar?->exists ?? false),

                // Cidade (obrigatório)
                Relation::make('parlamentar.cidade_id')
                    ->title('Cidade')
                    ->fromModel(Cidade::class, 'nome')
                    ->required()
                    ->help('Selecione a cidade à qual o parlamentar pertence.'),

                // Nome completo do parlamentar
                Input::make('parlamentar.nome_parlamentar')
                    ->title('Nome do Parlamentar')
                    ->required()
                    ->placeholder('Ex: João Silva'),

                // Status ativo/inativo
                CheckBox::make('parlamentar.ativo')
                    ->title('Ativo')
                    ->placeholder('Marque se o parlamentar está atualmente em exercício')
                    ->sendTrueOrFalse(),

                // URL da fotografia
                Input::make('parlamentar.fotografia')
                    ->title('URL da Fotografia')
                    ->placeholder('https://exemplo.com/foto.jpg')
                    ->help('URL pública da foto oficial do parlamentar'),

                // Partido atual (opcional)
                Relation::make('partido_atual_id')
                    ->title('Partido Atual')
                    ->fromModel(Partido::class, 'sigla', 'id')
                    ->displayAppend('nome')
                    ->nullable()
                    ->help('Selecione o partido atual. Ao salvar, será criada/atualizada a filiação vigente.'),
            ]),
        ];
    }

    /**
     * Salva os dados do parlamentar e sincroniza a filiação partidária atual.
     *
     * @param Request $request
     * @return void
     */
    public function save(Request $request): void
    {
        // Validação dos campos principais
        $validated = $request->validate([
            'parlamentar.cidade_id'       => ['required', 'integer', 'exists:cidades,id'],
            'parlamentar.nome_parlamentar'=> ['required', 'string', 'max:255'],
            'parlamentar.ativo'           => ['sometimes', 'boolean'],
            'parlamentar.fotografia'      => ['nullable', 'url', 'max:255'],
            'partido_atual_id'            => ['nullable', 'integer', 'exists:partidos,id'],
        ]);

        // Preenche os atributos do parlamentar
        $this->parlamentar->fill($validated['parlamentar']);

        // Garante um sapl_id padrão para novos registros (evita violação de constraint)
        if (!$this->parlamentar->exists && is_null($this->parlamentar->sapl_id)) {
            $this->parlamentar->sapl_id = 0;
        }

        $this->parlamentar->save();

        // Converte explicitamente para int ou null antes de passar ao método tipado
        $partidoId = $validated['partido_atual_id'] ?? null;
        $partidoId = $partidoId !== null ? (int) $partidoId : null;

        // Sincroniza a filiação partidária atual
        $this->sincronizarFiliacaoAtual($partidoId);

        Alert::success('Parlamentar salvo com sucesso.');
    }

    /**
     * Sincroniza a filiação partidária atual do parlamentar.
     *
     * - Se não houver partido informado, nada é feito.
     * - Se houver mudança de partido, desativa a filiação anterior e cria nova.
     * - Se o partido for o mesmo da filiação vigente, não altera nada.
     *
     * @param int|null $partidoId
     * @return void
     */
    protected function sincronizarFiliacaoAtual(?int $partidoId): void
    {
        if (is_null($partidoId)) {
            return;
        }

        $partido = Partido::find($partidoId);

        if (!$partido) {
            return;
        }

        // Busca filiação atual
        $filiacaoAtual = Filiacao::where('parlamentar_id', $this->parlamentar->id)
            ->where('atual', true)
            ->first();

        // Se já existe filiação atual com a mesma sigla, não faz nada
        if ($filiacaoAtual && $filiacaoAtual->partido_sigla === $partido->sigla) {
            return;
        }

        // Desativa filiação anterior, se existir
        if ($filiacaoAtual) {
            $filiacaoAtual->update([
                'atual'            => false,
                'data_desfiliacao' => now(),
            ]);
        }

        // Cria nova filiação vigente
        Filiacao::create([
            'parlamentar_id'   => $this->parlamentar->id,
            'cidade_id'        => $this->parlamentar->cidade_id,
            'sapl_id'          => $this->parlamentar->sapl_id,
            'partido_sigla'    => $partido->sigla,
            'partido_nome'     => $partido->nome,
            'data_filiacao'    => now(),
            'atual'            => true,
        ]);
    }
}