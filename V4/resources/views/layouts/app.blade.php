<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'BuscaLeis')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind / CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    {{-- ================= HEADER ================= --}}
    <header class="bg-white border-b">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="text-xl font-bold text-blue-600">
                BuscaLeis
            </a>

            {{-- Navegação --}}
            <nav class="flex gap-4 text-sm">
                <a href="{{ route('home') }}" class="hover:underline">
                    Início
                </a>

                {{-- 🚫 REMOVIDO: cidades.index --}}
                {{-- As cidades vivem apenas na HOME --}}
            </nav>
        </div>
    </header>

    {{-- ================= CONTENT ================= --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="bg-white border-t py-6 text-center text-sm text-gray-500">
        © {{ date('Y') }} BuscaLeis — Inteligência Legislativa
    </footer>

</body>
</html>
