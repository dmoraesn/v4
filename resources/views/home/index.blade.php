<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BuscaLeis — Inteligência Legislativa Municipal</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Animação do cursor piscante customizado */
        .cursor-blink {
            border-right: 2px solid #3b82f6;
            animation: blink 1s step-end infinite;
        }

        @keyframes blink {
            0%, 100% { border-color: transparent; }
            50% { border-color: #3b82f6; }
        }

        /* Utilitários para esconder scrollbar se necessário */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">

    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-100 rounded-full blur-3xl opacity-50 mix-blend-multiply"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-100 rounded-full blur-3xl opacity-50 mix-blend-multiply"></div>
    </div>

    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="bg-blue-600 text-white p-1.5 rounded-lg group-hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xl font-bold text-slate-800 tracking-tight">Busca<span class="text-blue-600">Leis</span></span>
            </a>
            
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="#" class="hover:text-blue-600 transition-colors">Sobre</a>
                <a href="#" class="hover:text-blue-600 transition-colors">API</a>
                <a href="#" class="px-4 py-2 bg-slate-900 text-white rounded-full hover:bg-slate-800 transition-colors">Acessar Sistema</a>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 py-12 md:py-20">

        <section class="text-center max-w-4xl mx-auto mb-20">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold uppercase tracking-wider mb-6 border border-blue-100">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Base de dados atualizada em tempo real
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-6 tracking-tight leading-tight">
                Inteligência Legislativa <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">ao seu alcance.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Pesquise leis, projetos e diários oficiais de centenas de municípios brasileiros em uma única plataforma unificada.
            </p>

            <div class="relative max-w-2xl mx-auto group">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full blur opacity-20 group-hover:opacity-40 transition duration-200"></div>
                
                <form action="{{ route('buscar') }}" method="GET" class="relative bg-white rounded-full shadow-xl flex items-center p-2 border border-slate-200 focus-within:ring-4 focus-within:ring-blue-100 focus-within:border-blue-400 transition-all">
                    
                    <div class="pl-4 pr-2 text-slate-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <div class="flex-1 relative h-12">
                        <input 
                            type="text" 
                            name="q" 
                            id="search-input" 
                            class="absolute inset-0 w-full h-full bg-transparent border-none outline-none text-lg text-slate-900 placeholder-transparent z-10 focus:ring-0 px-2"
                            autocomplete="off"
                            required
                        >
                        <div id="typing-container" class="absolute inset-0 flex items-center px-2 pointer-events-none text-lg text-slate-400 select-none">
                            <span id="typing-text"></span><span class="cursor-blink h-6"></span>
                        </div>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-3 transition-colors shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>

            <div class="mt-8 flex items-center justify-center gap-2 text-sm text-slate-500">
                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span><strong>{{ number_format($totalLeis ?? 0, 0, ',', '.') }}</strong> documentos indexados</span>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">
            
            <div class="lg:col-span-8 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                        Cidades Mais Ativas
                    </h2>
                    <a href="{{ route('cidades.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">Ver todas &rarr;</a>
                </div>

                <div class="grid gap-4">
                    @forelse($topCidades ?? [] as $index => $cidade)
                        @php
                            $urlBrasao = $cidade->brasao ?? null;
                            // Ajuste robusto para caminho da imagem
                            $urlFinal = $urlBrasao ? asset('storage/' . ltrim(str_replace(['public/', 'storage/'], '', $urlBrasao), '/')) : null;
                        @endphp
                        
                        <a href="{{ route('cidade.home', $cidade->slug) }}" class="group relative bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:border-blue-400 hover:shadow-md transition-all duration-300 flex items-center gap-5">
                            
                            <div class="hidden sm:flex flex-col items-center justify-center w-10 shrink-0">
                                <span class="text-3xl font-black text-slate-100 group-hover:text-blue-100 transition-colors">#{{ $index + 1 }}</span>
                            </div>

                            <div class="relative w-14 h-14 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden shrink-0 p-1 group-hover:scale-105 transition-transform">
                                @if($urlFinal)
                                    <img src="{{ $urlFinal }}" alt="{{ $cidade->nome }}" class="w-full h-full object-contain">
                                @else
                                    <span class="text-lg font-bold text-slate-300">{{ substr($cidade->nome, 0, 2) }}</span>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-slate-900 truncate group-hover:text-blue-600 transition-colors">
                                    {{ $cidade->nome }}
                                </h3>
                                <div class="flex items-center gap-3 text-sm text-slate-500 mt-0.5">
                                    <span class="bg-slate-100 px-2 py-0.5 rounded text-xs font-semibold text-slate-600">{{ $cidade->uf }}</span>
                                    <span>{{ number_format($cidade->total_leis_local ?? 0, 0, ',', '.') }} documentos</span>
                                </div>
                            </div>

                            <div class="text-slate-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center bg-white rounded-2xl border border-dashed border-slate-300 text-slate-500">
                            Nenhuma cidade rankeada no momento.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-4 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Em Alta
                    </h2>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 divide-y divide-slate-100 overflow-hidden">
                    @forelse($materiasMaisAcessadas ?? [] as $materia)
                        <a href="{{ route('materias.show', [$materia->cidade->slug, $materia->id]) }}" class="block p-4 hover:bg-slate-50 transition-colors group">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold uppercase rounded tracking-wider border border-blue-100">
                                    {{ $materia->tipo_sigla ?? 'DOC' }}
                                </span>
                                <span class="text-xs font-medium text-slate-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ number_format($materia->acessos, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <h4 class="text-sm font-medium text-slate-800 group-hover:text-blue-600 line-clamp-2 leading-relaxed mb-2">
                                {{ $materia->ementa }}
                            </h4>
                            
                            <p class="text-xs text-slate-500 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                {{ $materia->cidade->nome }} - {{ $materia->cidade->uf }}
                            </p>
                        </a>
                    @empty
                        <div class="p-8 text-center text-sm text-slate-500 italic">
                            Aguardando dados de acesso.
                        </div>
                    @endforelse
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="font-bold text-lg mb-2">É gestor público?</h3>
                        <p class="text-slate-300 text-sm mb-4">Modernize o legislativo da sua cidade integrando ao BuscaLeis.</p>
                        <a href="#" class="inline-block text-xs font-bold bg-white text-slate-900 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors">Saiba mais</a>
                    </div>
                    <div class="absolute right-[-20px] bottom-[-20px] text-slate-700 opacity-20 transform rotate-12">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"/></svg>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <footer class="border-t border-slate-200 bg-white py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <a href="#" class="inline-block text-2xl font-bold text-slate-300 hover:text-blue-600 transition-colors mb-4">BuscaLeis</a>
            <p class="text-slate-500 text-sm mb-8 max-w-md mx-auto">
                Transformando dados públicos em informação acessível para cidadãos e gestores de todo o Brasil.
            </p>
            <div class="flex justify-center gap-6 text-sm font-medium text-slate-600">
                <a href="#" class="hover:text-blue-600">Termos de Uso</a>
                <a href="#" class="hover:text-blue-600">Privacidade</a>
                <a href="#" class="hover:text-blue-600">Contato</a>
            </div>
            <div class="mt-8 text-xs text-slate-400">
                &copy; {{ date('Y') }} BuscaLeis. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const phrases = [
                "IPTU em Fortaleza...",
                "Lei Orgânica de São Paulo...",
                "Educação em Sobral...",
                "Orçamento de Belo Horizonte...",
                "Meio Ambiente em Manaus..."
            ];

            const typingText = document.getElementById('typing-text');
            const typingContainer = document.getElementById('typing-container');
            const input = document.getElementById('search-input');

            let phraseIndex = 0;
            let charIndex = 0;
            let isDeleting = false;
            let typeTimeout;

            function typeEffect() {
                const currentPhrase = phrases[phraseIndex];

                if (isDeleting) {
                    typingText.textContent = currentPhrase.substring(0, charIndex - 1);
                    charIndex--;
                } else {
                    typingText.textContent = currentPhrase.substring(0, charIndex + 1);
                    charIndex++;
                }

                // Logica de velocidade e troca de estado
                let typeSpeed = isDeleting ? 40 : 80;

                if (!isDeleting && charIndex === currentPhrase.length) {
                    typeSpeed = 2000; // Pausa no final da frase
                    isDeleting = true;
                } else if (isDeleting && charIndex === 0) {
                    isDeleting = false;
                    phraseIndex = (phraseIndex + 1) % phrases.length;
                    typeSpeed = 300; // Pausa antes de começar a próxima
                }

                typeTimeout = setTimeout(typeEffect, typeSpeed);
            }

            // Iniciar animação
            typeEffect();

            // Controle de Visibilidade baseado no foco/conteúdo
            function handleInputState() {
                if (document.activeElement === input || input.value.length > 0) {
                    typingContainer.style.opacity = '0';
                } else {
                    typingContainer.style.opacity = '1';
                }
            }

            input.addEventListener('focus', handleInputState);
            input.addEventListener('blur', handleInputState);
            input.addEventListener('input', handleInputState);
        });
    </script>
</body>
</html>