<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cidade->nome }} / {{ $cidade->uf }} — BuscaLeis</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }
        .glass-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); }
        .stat-card:hover { transform: translateY(-4px); transition: all 0.3s ease; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-black text-blue-600 tracking-tighter">
                Busca<span class="text-slate-800">Leis</span>
            </a>
            <a href="{{ route('home') }}" class="group flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Voltar à busca global
            </a>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto w-full px-6 py-10">

        {{-- Header da Cidade --}}
        <header class="bg-white rounded-3xl border border-slate-200 p-8 md:p-12 mb-10 shadow-sm flex flex-col md:flex-row items-center gap-8">
            <div class="relative">
                <div class="w-32 h-32 bg-slate-50 rounded-2xl flex items-center justify-center p-4 border border-slate-100 shadow-inner">
                    @if($cidade->brasao_url)
                        <img src="{{ $cidade->brasao_url }}" alt="Brasão de {{ $cidade->nome }}" class="w-full h-full object-contain">
                    @else
                        <span class="text-5xl">🏛️</span>
                    @endif
                </div>

                <div class="absolute -bottom-2 -right-2 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-lg uppercase tracking-wider shadow-lg">
                    {{ $cidade->uf }}
                </div>
            </div>

            <div class="text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-2">
                    {{ $cidade->nome }}
                </h1>
                <p class="text-lg text-slate-500 font-medium">
                    Portal de Inteligência Legislativa e Transparência Pública
                </p>
                <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        DADOS ATUALIZADOS
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                        SISTEMA SAPL ATIVO
                    </span>
                </div>
            </div>
        </header>

        {{-- Busca Interna --}}
        <section class="mb-12">
            <form action="{{ route('materias.index', $cidade->slug) }}" method="GET" class="relative group">
                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="O que você deseja encontrar em {{ $cidade->nome }}? (Ex: IPTU, Meio Ambiente, Nome de Rua...)"
                       class="w-full pl-14 pr-32 py-5 bg-white border-2 border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all text-lg shadow-sm"
                       autofocus>
                <button type="submit" class="absolute right-3 top-2 bottom-2 bg-blue-600 hover:bg-blue-700 text-white px-6 rounded-xl font-bold transition-all active:scale-95">
                    Pesquisar
                </button>
            </form>
        </section>

        {{-- Grid de Estatísticas --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <div class="stat-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl">📄</div>
                <div>
                    <span class="block text-2xl font-black text-slate-900 leading-none">
                        {{ number_format($totalMaterias, 0, ',', '.') }}
                    </span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Matérias</span>
                </div>
            </div>

            <div class="stat-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-2xl">⚖️</div>
                <div>
                    <span class="block text-2xl font-black text-slate-900 leading-none">
                        {{ number_format($totalLeisNormas, 0, ',', '.') }}
                    </span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Leis e Normas</span>
                </div>
            </div>

            <div class="stat-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-2xl">👤</div>
                <div>
                    <span class="block text-2xl font-black text-slate-900 leading-none">
                        {{ number_format($totalParlamentaresAtivos, 0, ',', '.') }}
                    </span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Parlamentares Ativos</span>
                </div>
            </div>
        </section>

        {{-- Listagem Principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- Leis mais acessadas (8 colunas) --}}
            <section class="lg:col-span-8">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Leis mais acessadas</h2>
                        <p class="text-sm text-slate-500">Documentos com maior relevância pública</p>
                    </div>
                    <a href="{{ route('materias.index', $cidade->slug) }}" class="text-sm font-bold text-blue-600 hover:underline">
                        Ver todas →
                    </a>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    @forelse($leisMaisAcessadas as $lei)
                        <a href="{{ route('materias.show', [$cidade->slug, $lei->id]) }}" class="group flex items-center justify-between p-5 border-b border-slate-100 hover:bg-slate-50 transition-colors last:border-0">
                            <div class="flex-grow pr-4">
                                <h3 class="font-semibold text-slate-700 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    {{ $lei->ementa }}
                                </h3>
                                <div class="flex gap-3 mt-2">
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-500 rounded uppercase">
                                        {{ $lei->tipo_sigla ?? 'MAT' }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 text-blue-600 rounded uppercase">
                                        Acessado recentemente
                                    </span>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-300 group-hover:text-blue-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @empty
                        <div class="p-10 text-center text-slate-400">
                            Nenhum dado disponível no momento.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Parlamentares Ativos (4 colunas) --}}
            <section class="lg:col-span-4">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Parlamentares Ativos</h2>
                    <p class="text-sm text-slate-500">Top 5 por quantidade de matérias apresentadas na legislatura atual</p>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                    @forelse($topParlamentares as $parlamentar)
                        <div class="p-4 border-b border-slate-100 last:border-0 flex items-center gap-4 hover:bg-slate-50 transition-colors">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ strtoupper(substr($parlamentar->nome_parlamentar, 0, 1)) }}
                            </div>
                            <div class="flex-grow">
                                <div class="font-bold text-slate-700 text-base">
                                    {{ $parlamentar->nome_parlamentar }}
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    @if($parlamentar->filiacaoAtual && $parlamentar->filiacaoAtual->partido_sigla)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white shadow-sm">
                                            {{ $parlamentar->filiacaoAtual->partido_sigla }}
                                        </span>
                                    @else
                                        <span class="text-xs font-medium text-slate-400 italic">
                                            Sem partido
                                        </span>
                                    @endif

                                    <span class="text-sm font-bold text-slate-700">
                                        {{ number_format($parlamentar->quantidade_materias, 0, ',', '.') }} matéria{{ $parlamentar->quantidade_materias !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 italic">
                            Dados em atualização para a legislatura atual.
                        </div>
                    @endforelse

                    <a href="{{ route('cidade.autores', $cidade->slug) }}" class="block p-4 bg-slate-50 text-center text-sm font-bold text-slate-600 hover:text-blue-600 border-t border-slate-100 transition-colors">
                        Ver lista completa de parlamentares
                    </a>
                </div>
            </section>

        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-10 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm text-slate-500 font-medium">
                © {{ date('Y') }} BuscaLeis — Transformando dados públicos em inteligência legislativa municipal.
            </p>
            <div class="mt-4 flex justify-center gap-6">
                <a href="#" class="text-xs font-bold text-slate-400 hover:text-slate-600 uppercase">Termos de Uso</a>
                <a href="#" class="text-xs font-bold text-slate-400 hover:text-slate-600 uppercase">Privacidade</a>
                <a href="#" class="text-xs font-bold text-slate-400 hover:text-slate-600 uppercase">Sobre o SAPL</a>
            </div>
        </div>
    </footer>

</body>
</html>