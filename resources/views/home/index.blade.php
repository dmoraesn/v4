<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BuscaLeis — Inteligência Legislativa Municipal</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-blue-100">

    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-blue-100/50 blur-[120px]"></div>
        <div class="absolute top-[60%] -right-[5%] w-[30%] h-[30%] rounded-full bg-indigo-50 blur-[100px]"></div>
    </div>

    <nav class="sticky top-0 z-50 glass border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight hover:opacity-80 transition-opacity">
                Busca<span class="text-blue-600">Leis</span>
            </a>
            <div class="hidden md:flex gap-8 text-sm font-medium text-slate-600">
                <a href="#" class="hover:text-blue-600 transition-colors">Sobre</a>
                <a href="#" class="hover:text-blue-600 transition-colors">Documentação</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-12 md:py-24">
        
        <section class="text-center mb-20">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 mb-6 italic">
                Inteligência <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-500">Legislativa</span>
            </h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                Explore leis e projetos municipais de forma rápida e inteligente através da maior base de dados legislativos do Brasil.
            </p>

            <div class="max-w-2xl mx-auto relative group">
                <form action="{{ route('buscar') }}" method="GET">
                    <div class="relative flex items-center">
                        <input 
                            type="text" 
                            name="q" 
                            placeholder="Ex: IPTU, Plano Diretor, São Paulo..." 
                            class="w-full pl-6 pr-14 py-5 bg-white border border-slate-200 rounded-2xl shadow-sm group-hover:shadow-md focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all text-lg"
                            required
                        >
                        <button type="submit" class="absolute right-3 p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all shadow-lg shadow-blue-200 active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
                <p class="mt-4 text-sm text-slate-400">
                    <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 text-slate-600 font-semibold mr-1">
                        {{ number_format($totalLeis ?? 0, 0, ',', '.') }}
                    </span> 
                    documentos oficiais indexados em tempo real.
                </p>
            </div>
        </section>

        <section>
            <div class="flex items-end justify-between mb-8 border-b border-slate-200 pb-4">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-widest text-blue-600">Cobertura</h2>
                    <p class="text-2xl font-bold text-slate-800">Cidades em destaque</p>
                </div>
                <a href="{{ route('cidades.index') }}" class="group flex items-center text-sm font-bold text-slate-500 hover:text-blue-600 transition-colors">
                    Ver todas 
                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($cidades ?? [] as $slug => $cidade)
                    <a href="{{ route('cidade.home', $slug) }}" class="group relative bg-white border border-slate-200 p-5 rounded-2xl hover:border-blue-500/50 hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-300 overflow-hidden">
                        
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-10 -mt-10 opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <div class="relative flex items-center gap-4">
                            <div class="w-14 h-14 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-100 group-hover:bg-blue-50 transition-colors shrink-0 overflow-hidden shadow-inner">
                                @if(isset($cidade['bandeira_url']))
                                    <img src="{{ $cidade['bandeira_url'] }}" alt="{{ $cidade['nome'] }}" class="w-full h-full object-cover shadow-sm">
                                @else
                                    <span class="text-sm font-bold text-slate-400 group-hover:text-blue-600 uppercase">
                                        {{ substr($cidade['nome'], 0, 2) }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                    {{ $cidade['nome'] }}
                                </h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
                                    <span class="text-xs font-medium text-slate-500 uppercase tracking-tighter">{{ $cidade['uf'] }}</span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="block text-sm font-bold text-slate-900">{{ number_format($cidade['total_docs'] ?? 0, 0, ',', '.') }}</span>
                                <span class="block text-[10px] uppercase font-extrabold text-slate-400 tracking-wider">Documentos</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center bg-slate-100/50 rounded-2xl border-2 border-dashed border-slate-200">
                        <p class="text-slate-500 font-medium">Nenhuma cidade disponível para exibição.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="mt-20 border-t border-slate-200 py-12 bg-white">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <p class="text-sm text-slate-500">
                &copy; {{ date('Y') }} <span class="font-bold text-slate-900">BuscaLeis</span>. Inteligência Legislativa Municipal.
            </p>
            <div class="flex gap-6 text-sm font-semibold text-slate-400">
                <a href="#" class="hover:text-slate-900 transition-colors">Privacidade</a>
                <a href="#" class="hover:text-slate-900 transition-colors">Termos</a>
            </div>
        </div>
    </footer>

</body>
</html>