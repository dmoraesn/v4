<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BuscaLeis — Gestão e Inteligência Legislativa</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0055ff;
            --primary-hover: #0044cc;
            --bg: #ffffff;
            --text-dark: #111827;
            --text-light: #6b7280;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            margin: 0;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }

        .brand-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .brand-title {
            font-size: 3rem;
            font-weight: 700;
            letter-spacing: -2px;
            margin: 0;
        }

        .brand-title span {
            color: var(--primary);
        }

        .brand-subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 500;
            margin-top: 6px;
        }

        .search-area {
            width: 100%;
            max-width: 650px;
        }

        .search-form {
            display: flex;
            align-items: center;
            background: #fff;
            border: 2px solid var(--border);
            border-radius: 16px;
            padding: 6px;
            transition: all 0.3s ease;
        }

        .search-form:focus-within {
            border-color: var(--primary);
            box-shadow: 0 10px 25px -5px rgba(0, 85, 255, 0.15);
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 14px 18px;
            font-size: 1.1rem;
            outline: none;
            background: transparent;
        }

        .search-button {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 14px 22px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-button:hover {
            background: var(--primary-hover);
        }

        .suggestions {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .sugg-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .sugg-tag {
            font-size: 0.85rem;
            background: #f3f4f6;
            padding: 6px 14px;
            border-radius: 999px;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.2s;
        }

        .sugg-tag:hover {
            background: #fff;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .stats-divider {
            width: 100%;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--border), transparent);
            margin: 40px 0;
        }

        .stats-container {
            text-align: center;
            font-size: 0.95rem;
            color: var(--text-light);
        }

        footer {
            padding: 30px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
            border-top: 1px solid #f3f4f6;
        }
    </style>
</head>
<body>

<div class="hero">

    <div class="brand-container">
        <h1 class="brand-title">Busca<span>Leis</span></h1>
        <div class="brand-subtitle">Gestão e Inteligência Legislativa</div>
    </div>

    {{-- BUSCADOR --}}
    <div class="search-area">
        <form action="{{ route('buscar') }}" method="GET" class="search-form">
            <input
                type="text"
                name="q"
                class="search-input"
                placeholder="Buscar leis, autores ou temas…"
                autocomplete="off"
                value="{{ request('q') }}"
            >

            <button type="submit" class="search-button">
                <svg viewBox="0 0 24 24" width="18" height="18">
                    <circle cx="11" cy="11" r="8" stroke="white" fill="none" stroke-width="2"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65" stroke="white" stroke-width="2"></line>
                </svg>
                Buscar
            </button>
        </form>

        <div class="suggestions">
            <span class="sugg-label">Sugestões:</span>
            <a href="{{ route('buscar', ['q' => 'Lei Orgânica']) }}" class="sugg-tag">Lei Orgânica</a>
            <a href="{{ route('buscar', ['q' => 'Plano Diretor']) }}" class="sugg-tag">Plano Diretor</a>
            <a href="{{ route('buscar', ['q' => 'Meio Ambiente']) }}" class="sugg-tag">Meio Ambiente</a>
            <a href="{{ route('buscar', ['q' => 'Impostos']) }}" class="sugg-tag">Impostos</a>
        </div>
    </div>

    {{-- ESTATÍSTICAS --}}
    <div class="stats-divider"></div>

    <div class="stats-container">
        Buscando entre
        <strong>{{ number_format($totalMaterias, 0, ',', '.') }}</strong>
        leis e matérias legislativas
        <br><br>
        {{ $cidadesPrincipais }}
        e mais <strong>{{ $totalCidades - $cidadesPrincipaisCount }}</strong> cidades usam o BuscaLeis.
        <a href="{{ route('cidades') }}">(ver todas)</a>
    </div>

</div>

<footer>
    © {{ date('Y') }} BuscaLeis — Transformando dados públicos em inteligência.
</footer>

</body>
</html>
