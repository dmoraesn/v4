<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cidade['nome'] ?? 'Cidade' }} / {{ $cidade['uf'] ?? '' }} — BuscaLeis</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0055ff;
            --primary-hover: #0044cc;
            --bg: #f3f4f6;
            --text-dark: #111827;
            --text-light: #6b7280;
            --border: #e5e7eb;
            --card-bg: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            background: var(--bg);
            color: var(--text-dark);
            line-height: 1.5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* Top Bar / Breadcrumb */
        .nav-back {
            margin-bottom: 24px;
        }
        .nav-back a {
            text-decoration: none;
            color: var(--text-light);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nav-back a:hover { color: var(--primary); }

        /* Cabeçalho da Cidade */
        .city-header {
            display: flex;
            align-items: center;
            gap: 24px;
            background: var(--card-bg);
            padding: 32px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 32px;
        }

        .city-header img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .city-title h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .city-title p {
            margin: 6px 0 0;
            color: var(--text-light);
            font-size: 15px;
        }

        /* Busca Destacada */
        .search-container {
            margin-bottom: 40px;
        }

        .search-form {
            display: flex;
            background: #fff;
            padding: 8px;
            border-radius: 16px;
            border: 2px solid var(--border);
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
        }

        .search-form:focus-within {
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
        }

        .search-form input {
            flex: 1;
            border: none;
            padding: 12px 16px;
            font-size: 16px;
            outline: none;
            color: var(--text-dark);
        }

        .search-form button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .search-form button:hover { background: var(--primary-hover); }

        /* Grid de Estatísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-3px); }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-info strong {
            display: block;
            font-size: 24px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .stat-info span {
            font-size: 14px;
            color: var(--text-light);
            font-weight: 500;
        }

        /* Listas e Seções */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 32px;
        }

        @media (max-width: 850px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .section-title h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .section-title a {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .data-list {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .data-item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .data-item:last-child { border-bottom: none; }
        .data-item:hover { background: #f9fafb; }

        .data-item a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 15px;
            flex: 1;
        }

        .badge {
            background: #f3f4f6;
            color: var(--text-light);
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .author-link {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .author-avatar {
            width: 32px;
            height: 32px;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }

        footer {
            margin-top: 64px;
            padding-top: 32px;
            border-top: 1px solid var(--border);
            text-align: center;
            font-size: 14px;
            color: var(--text-light);
        }
    </style>
</head>
<body>

<div class="container">
    
    <nav class="nav-back">
        <a href="{{ route('home') }}">← Voltar para a busca global</a>
    </nav>

    {{-- Header --}}
    <header class="city-header">
        @if(!empty($cidade['brasao']))
            <img src="{{ $cidade['brasao'] }}" alt="Brasão de {{ $cidade['nome'] }}">
        @else
            <div style="font-size: 40px;">🏛️</div>
        @endif
        <div class="city-title">
            <h1>{{ $cidade['nome'] ?? 'Cidade' }} / {{ $cidade['uf'] ?? 'UF' }}</h1>
            <p>Portal Legislativo de Inteligência e Transparência</p>
        </div>
    </header>

    {{-- Search --}}
    <section class="search-container">
        <form class="search-form" method="GET" action="{{ route('materias.index', $cidade['slug']) }}">
            <input 
                type="text" 
                name="q" 
                placeholder="O que você deseja encontrar em {{ $cidade['nome'] }}?" 
                value="{{ request('q') }}"
                autocomplete="off"
            >
            <button type="submit">Pesquisar</button>
        </form>
    </section>

    {{-- Stats Grid --}}
    <section class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📄</div>
            <div class="stat-info">
                <strong>{{ number_format($stats['total_materias'] ?? 0, 0, ',', '.') }}</strong>
                <span>Matérias legislativas</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚖️</div>
            <div class="stat-info">
                <strong>{{ number_format($stats['total_leis'] ?? 0, 0, ',', '.') }}</strong>
                <span>Leis e normas</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👤</div>
            <div class="stat-info">
                <strong>{{ number_format($stats['total_autores'] ?? 0, 0, ',', '.') }}</strong>
                <span>Parlamentares</span>
            </div>
        </div>
    </section>

    {{-- Dashboard Content --}}
    <div class="dashboard-grid">
        
        {{-- Coluna Principal: Leis --}}
        <section>
            <div class="section-title">
                <h2>Leis mais acessadas</h2>
                <a href="{{ route('materias.index', $cidade['slug']) }}">Ver todas</a>
            </div>
            <div class="data-list">
                @forelse($leisMaisAcessadas ?? [] as $lei)
                    <div class="data-item">
                        <a href="{{ route('materias.show', [$cidade['slug'], $lei['id']]) }}">
                            {{ Str::limit($lei['titulo'], 80) }}
                        </a>
                        <span class="badge">{{ $lei['tipo'] }}</span>
                    </div>
                @empty
                    <div class="data-item" style="color: var(--text-light);">Nenhuma lei disponível no momento.</div>
                @endforelse
            </div>
        </section>

        {{-- Coluna Lateral: Vereadores --}}
        <section>
            <div class="section-title">
                <h2>Parlamentares ativos</h2>
                <a href="{{ route('cidade.autores', $cidade['slug']) }}">Lista completa</a>
            </div>
            <div class="data-list">
                @forelse($autoresMaisBuscados ?? [] as $autor)
                    <div class="data-item">
                        <div class="author-link">
                            <div class="author-avatar" style="background-color: var(--primary);">
                                {{ strtoupper(substr($autor['nome'], 0, 1)) }}
                            </div>
                            <a href="{{ $autor['url'] }}">{{ $autor['nome'] }}</a>
                        </div>
                    </div>
                @empty
                    <div class="data-item">Nenhum autor encontrado.</div>
                @endforelse
            </div>
        </section>

    </div>

    <footer>
        <p>© {{ date('Y') }} BuscaLeis — Transformando dados públicos em inteligência legislativa.</p>
    </footer>

</div>

</body>
</html>