<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cidades Indexadas — BuscaLeis</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #0f172a; }
        .city-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Navbar simples com logo e link home --}}
    <nav class="bg-white border-b border-slate-200 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-2xl font-black text-blue-600">Busca<span class="text-slate-800">Leis</span></a>
            <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600">← Voltar à busca</a>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-6 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-slate-900 mb-4">Cidades Indexadas</h1>
            <p class="text-lg text-slate-600">
                Atualmente indexamos documentos legislativos de 
                <strong>{{ count($cidades) }}</strong> 
                {{ Str::plural('município', count($cidades)) }} com sistema SAPL ativo.
            </p>
        </div>

        @if(count($cidades) === 0)
            <div class="text-center py-20">
                <p class="text-slate-500">Nenhuma cidade indexada no momento.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($cidades as $slug => $cidade)
                    <a href="{{ route('cidade.home', $slug) }}" 
                       class="city-card block bg-white rounded-2xl border border-slate-200 p-8 text-center transition-all duration-300">
                        @if($cidade['brasao'])
                            <img src="{{ $cidade['brasao'] }}" 
                                 alt="Brasão de {{ $cidade['nome'] }}" 
                                 class="w-24 h-24 mx-auto mb-6 object-contain rounded-full border border-slate-100">
                        @else
                            <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center">
                                <span class="text-3xl font-bold text-slate-400">
                                    {{ Str::substr($cidade['nome'], 0, 2) }}
                                </span>
                            </div>
                        @endif
                        <h3 class="text-xl font-semibold text-slate-800 mb-2">{{ $cidade['nome'] }}</h3>
                        <p class="text-sm text-slate-500">{{ $cidade['uf'] }}</p>
                        <span class="inline-block mt-4 text-sm font-medium text-blue-600 hover:underline">
                            Acessar legislatura →
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-sm text-slate-500">
        © {{ date('Y') }} BuscaLeis — Inteligência Legislativa Municipal
    </footer>
</body>
</html>