<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">

    <title>
        @if($isBusca)
            Resultados para "{{ $q }}" — {{ $cidade['nome'] }}
        @else
            Matérias Legislativas — {{ $cidade['nome'] }}
        @endif
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 text-slate-900 antialiased">

{{-- ===================== HEADER ===================== --}}
<header class="bg-white border-b sticky top-0 z-50">
    <div class="max-w-5xl mx-auto px-6 py-4 flex items-center gap-6">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="text-xl font-extrabold text-blue-600 tracking-tight">
            BuscaLeis
        </a>

        {{-- Busca local --}}
        <form action="{{ route('materias.index', $cidade['slug']) }}" method="GET" class="flex-1">
            <input
                type="text"
                name="q"
                value="{{ $q }}"
                placeholder="Pesquisar em {{ $cidade['nome'] }}…"
                class="w-full rounded-full border border-slate-300 px-6 py-2
                       focus:ring-4 focus:ring-blue-200 outline-none"
            >
        </form>

        {{-- Badge cidade --}}
        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold whitespace-nowrap">
            {{ $cidade['nome'] }} / {{ $cidade['uf'] }}
        </span>
    </div>
</header>

{{-- ===================== CONTEÚDO ===================== --}}
<main class="max-w-5xl mx-auto px-6 py-10">

    {{-- Estatísticas --}}
    <div class="mb-8 text-sm text-slate-600">
        @if($isBusca)
            {{ number_format($pagination['total_entries'], 0, ',', '.') }}
            documentos encontrados —
            página {{ $pagination['page'] }} de {{ $pagination['total_pages'] }}
        @else
            {{ number_format($pagination['total_entries'], 0, ',', '.') }}
            documentos legislativos indexados em {{ $cidade['nome'] }}
        @endif
    </div>

    {{-- ===================== LISTA DE RESULTADOS ===================== --}}
    <div class="space-y-8">

        @forelse($materias['dados'] as $materia)
            <article class="bg-white border border-slate-200 rounded-2xl p-6
                            hover:shadow-lg hover:border-blue-200 transition">

                {{-- Cabeçalho --}}
                <div class="flex flex-wrap items-center justify-between gap-4 mb-3 text-xs uppercase font-semibold text-slate-500">

                    {{-- Tipo / Número / Ano --}}
                    <span>
                        {{ $materia['__str__'] ?? 'Matéria Legislativa' }}
                    </span>

                    {{-- Status --}}
                    @if($materia['em_tramitacao'] ?? false)
                        <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700">
                            Em tramitação
                        </span>
                    @else
                        <span class="px-2 py-1 rounded bg-rose-100 text-rose-700">
                            Finalizada
                        </span>
                    @endif
                </div>

                {{-- Título / Ementa --}}
                <h2 class="text-lg md:text-xl font-bold text-blue-700 leading-snug mb-3">
                    <a href="{{ route('materias.show', ['cidade' => $cidade['slug'], 'id' => $materia['id']]) }}">
                        {!! $materia['ementa_highlight'] ?? e($materia['ementa'] ?? 'Ementa não informada.') !!}
                    </a>
                </h2>

                {{-- Snippet --}}
                <p class="text-sm text-slate-600 leading-relaxed">
                    {!! \Illuminate\Support\Str::limit(strip_tags($materia['ementa'] ?? ''), 240) !!}
                </p>

                {{-- Meta --}}
                <div class="flex flex-wrap items-center gap-5 text-sm text-slate-600 mt-5">

                    {{-- Data --}}
                    <span class="flex items-center gap-1">
                        📅
                        {{ \Carbon\Carbon::parse($materia['data_apresentacao'])->format('d/m/Y') }}
                    </span>

                    {{-- Autor --}}
                    @if(!empty($materia['autor']))
                        <span class="flex items-center gap-1">
                            👤
                            <a href="{{ $materia['autor']['url'] }}"
                               target="_blank"
                               class="font-semibold text-blue-600 hover:underline">
                                {{ $materia['autor']['nome'] }}
                            </a>
                        </span>
                    @endif
                </div>
            </article>
        @empty
            <div class="text-center py-16 text-slate-500">
                Nenhum resultado encontrado.
            </div>
        @endforelse

    </div>

    {{-- ===================== PAGINAÇÃO ===================== --}}
    @if($pagination['total_pages'] > 1)
        <nav class="flex justify-center items-center gap-2 mt-14 flex-wrap">

            {{-- Anterior --}}
            @if($pagination['previous_page'])
                <a href="?page={{ $pagination['previous_page'] }}&q={{ $q }}"
                   class="px-3 py-2 rounded border bg-white hover:bg-slate-100 text-sm">
                    ←
                </a>
            @endif

            @php
                $current = $pagination['page'];
                $total = $pagination['total_pages'];
                $start = max(1, $current - 2);
                $end = min($total, $current + 2);
            @endphp

            @if($start > 1)
                <a href="?page=1&q={{ $q }}" class="px-3 py-2 text-sm">1</a>
                <span class="px-2">…</span>
            @endif

            @for($i = $start; $i <= $end; $i++)
                <a href="?page={{ $i }}&q={{ $q }}"
                   class="px-3 py-2 rounded text-sm
                   {{ $i === $current
                        ? 'bg-blue-600 text-white font-bold'
                        : 'bg-white border hover:bg-slate-100' }}">
                    {{ $i }}
                </a>
            @endfor

            @if($end < $total)
                <span class="px-2">…</span>
                <a href="?page={{ $total }}&q={{ $q }}" class="px-3 py-2 text-sm">
                    {{ $total }}
                </a>
            @endif

            {{-- Próxima --}}
            @if($pagination['next_page'])
                <a href="?page={{ $pagination['next_page'] }}&q={{ $q }}"
                   class="px-3 py-2 rounded border bg-white hover:bg-slate-100 text-sm">
                    →
                </a>
            @endif
        </nav>
    @endif

</main>

</body>
</html>
