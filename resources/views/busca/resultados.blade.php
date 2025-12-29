@extends('layouts.app')

@section('title', 'Busca global por "' . $q . '" — BuscaLeis')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- 🔍 Barra de busca global --}}
    <form action="{{ route('buscar') }}" method="GET" class="mb-8">
        <label class="block text-sm font-semibold text-gray-600 mb-2">
            Busca global em todas as cidades
        </label>
        <div class="flex gap-2">
            <input
                type="text"
                name="q"
                value="{{ $q }}"
                class="w-full border rounded px-4 py-2"
                placeholder="Ex: IPTU, plano diretor, zoneamento..."
                autofocus
            >
            <button class="bg-blue-600 text-white px-4 py-2 rounded font-semibold">
                Buscar
            </button>
        </div>
    </form>

    {{-- Estatísticas --}}
    <h1 class="text-lg font-bold mb-2">
        Aproximadamente {{ $resultados['total'] }} resultados encontrados
    </h1>

    <p class="text-sm text-gray-500 mb-6">
        Página {{ $resultados['page'] }} de {{ $resultados['total_pages'] }}
    </p>

    {{-- Resultados --}}
    <div class="space-y-6">
        @forelse($resultados['dados'] as $item)
            <div class="bg-white p-4 rounded shadow-sm">

                {{-- Badge da cidade --}}
                <span class="inline-block mb-2 text-xs font-bold bg-blue-100 text-blue-700 px-2 py-1 rounded">
                    {{ $item['cidade']['nome'] }} / {{ $item['cidade']['uf'] }}
                </span>

                {{-- Link --}}
                <a href="{{ route('materias.show', [
                    'cidade' => $item['cidade']['slug'],
                    'id' => $item['id']
                ]) }}"
                class="block text-blue-700 font-semibold hover:underline">
                    {{ $item['numero'] }}/{{ $item['ano'] }}
                </a>

                {{-- Ementa --}}
                <p class="mt-2 text-gray-700 leading-relaxed">
                    {!! $item['ementa'] !!}
                </p>
            </div>
        @empty
            <p>Nenhum resultado relevante encontrado.</p>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div class="mt-8 flex gap-4">
        @if($resultados['page'] > 1)
            <a href="?q={{ $q }}&page={{ $resultados['page'] - 1 }}" class="underline">
                ← Anterior
            </a>
        @endif

        @if($resultados['page'] < $resultados['total_pages'])
            <a href="?q={{ $q }}&page={{ $resultados['page'] + 1 }}" class="underline">
                Próxima →
            </a>
        @endif
    </div>

</div>
@endsection
