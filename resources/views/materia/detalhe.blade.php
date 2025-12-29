@extends('layouts.app')

@section('title', ($materia['tipo']['sigla'] ?? 'Matéria') . ' ' . ($materia['numero'] ?? '') . '/' . ($materia['ano'] ?? '') . ' — BuscaLeis')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    
    {{-- Navegação Superior --}}
    <nav class="mb-6">
        @php $cidadeSlug = $cidade['slug'] ?? request()->route('cidade'); @endphp
        <a href="{{ route('materias.index', ['cidade' => $cidadeSlug]) }}" 
           class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Voltar para matérias em {{ $cidade['nome'] ?? 'Câmara' }}
        </a>
    </nav>

    <article class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        
        {{-- Banner de Identificação --}}
        <div class="bg-slate-900 text-white px-8 py-12 relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10">
                <svg width="200" height="200" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
            </div>
            
            <div class="relative z-10">
                <span class="inline-block px-3 py-1 rounded-md bg-blue-500/30 text-blue-300 text-xs font-bold uppercase tracking-widest mb-4">
                    {{ $materia['tipo']['descricao'] ?? 'Documento Legislativo' }}
                </span>
                <h1 class="text-3xl md:text-5xl font-black tracking-tight">
                    {{ $materia['tipo']['sigla'] ?? 'MAT' }} {{ $materia['numero'] ?? '' }}/{{ $materia['ano'] ?? '' }}
                </h1>
                
                <div class="mt-6 flex flex-wrap gap-4 items-center">
                    <div class="flex items-center gap-2 text-slate-300 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                        Lido em Plenário em {{ \Carbon\Carbon::parse($materia['data_apresentacao'] ?? now())->format('d/m/Y') }}
                    </div>
                    @php $emTramitacao = $materia['em_tramitacao'] ?? false; @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $emTramitacao ? 'border-green-500 text-green-400' : 'border-red-500 text-red-400' }}">
                        ● {{ $emTramitacao ? 'EM TRAMITAÇÃO' : 'FINALIZADA' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12 space-y-12">
            
            {{-- Ementa Principal --}}
            <section>
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Conteúdo / Ementa</h2>
                <div class="relative">
                    <div class="absolute -left-4 top-0 bottom-0 w-1 bg-blue-600 rounded-full"></div>
                    <p class="text-xl md:text-2xl text-slate-800 font-serif leading-relaxed italic">
                        "{{ $materia['ementa'] ?? 'Ementa não disponibilizada.' }}"
                    </p>
                </div>
            </section>

            {{-- Grid de Informações --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <section>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Autoria</h2>
                    <div class="space-y-4">
                        @forelse($autores ?? [] as $autor)
                            <div class="flex items-center gap-4 group">
                                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-blue-600 font-bold group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    {{ substr($autor['nome'] ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900">{{ $autor['nome'] ?? 'Autor Desconhecido' }}</p>
                                    <p class="text-xs text-slate-500 uppercase tracking-tighter">Parlamentar / Proponente</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-sm italic">Informação de autoria indisponível.</p>
                        @endforelse
                    </div>
                </section>

                <section>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-6">Detalhes Técnicos</h2>
                    <dl class="space-y-4 text-sm">
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Regime de Tramitação</dt>
                            <dd class="font-bold text-slate-800">{{ $materia['regime_tramitacao']['descricao'] ?? 'Ordinário' }}</dd>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <dt class="text-slate-500">Ano da Matéria</dt>
                            <dd class="font-bold text-slate-800">{{ $materia['ano'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </section>
            </div>

            {{-- Botão de Download --}}
            @if(!empty($materia['texto_integral']))
            <section class="bg-blue-50 rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-lg font-bold text-blue-900">Documentação Original</h3>
                    <p class="text-sm text-blue-700">Acesse o arquivo PDF completo assinado eletronicamente.</p>
                </div>
                <a href="{{ $materia['texto_integral'] }}" target="_blank" 
                   class="w-full md:w-auto px-8 py-4 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all text-center shadow-lg shadow-blue-200">
                    Visualizar PDF Original
                </a>
            </section>
            @endif

        </div>
    </article>
</div>
@endsection