<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Matérias Legislativas</title>

    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h1>Matérias Legislativas</h1>

<table>
    <thead>
        <tr>
            <th>Nº</th>
            <th>Ano</th>
            <th>Ementa</th>
            <th>Apresentação</th>
            <th>Tramitação</th>
        </tr>
    </thead>
    <tbody>
    @foreach($materias as $materia)
        <tr>
            <td>
                <a href="{{ route('materias.show', $materia['id']) }}">
                    {{ $materia['numero'] }}
                </a>
            </td>
            <td>{{ $materia['ano'] }}</td>
            <td>{{ $materia['ementa'] }}</td>
            <td>{{ $materia['data_apresentacao'] }}</td>
            <td>
                {{ $materia['em_tramitacao'] ? 'Em tramitação' : 'Finalizada' }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if(!empty($pagination))
    <p>
        Página {{ $pagination['page'] }} de {{ $pagination['total_pages'] }}
    </p>

    @if($pagination['previous_page'])
        <a href="?page={{ $pagination['previous_page'] }}">← Anterior</a>
    @endif

    @if($pagination['next_page'])
        <a href="?page={{ $pagination['next_page'] }}">Próxima →</a>
    @endif
@endif

</body>
</html>
