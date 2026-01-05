<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $parlamentar->nome_parlamentar }} - BuscaLeis</title>

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
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-dark);
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px 60px;
        }

        /* Voltar */
        .nav-back {
            margin-bottom: 24px;
        }

        .nav-back a {
            text-decoration: none;
            color: var(--text-light);
            font-size: 14px;
        }

        .nav-back a:hover {
            color: var(--primary);
        }

        /* Cabeçalho do perfil */
        .profile-header {
            background: linear-gradient(135deg, #0055ff 0%, #0044cc 100%);
            color: white;
            padding: 40px 32px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 700;
            border: 6px solid white;
        }

        .profile-info h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
        }

        .profile-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }

        .profile-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
        }

        /* Estatísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 48px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .stat-value {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-light);
        }

        .status-active {
            color: #10b981;
        }

        /* Seção de matérias */
        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .materias-list {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .materia-item {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            transition: background 0.2s;
        }

        .materia-item:hover {
            background: #f8fafc;
        }

        .materia-item:last-child {
            border-bottom: none;
        }

        .materia-title {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-dark);
            margin-bottom: 8px;
            line-clamp: 2;
        }

        .materia-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            color: var(--text-light);
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--text-light);
        }

        /* Resumo IA futuro */
        .ia-summary {
            background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            margin-top: 48px;
        }

        footer {
            margin-top: 64px;
            text-align: center;
            font-size: 13px;
            color: var(--text-light);
        }
    </style>
</head>
<body>
<div class="container">

    <div class="nav-back">
        <a href="{{ route('cidade.autores', $parlamentar->cidade->slug) }}">← Voltar para lista de parlamentares</a>
    </div>

    <!-- Cabeçalho do perfil -->
    <header class="profile-header">
        <div class="profile-avatar">
            {{ strtoupper(substr($parlamentar->nome_parlamentar, 0, 1)) }}
        </div>
        <div class="profile-info">
            <h1>{{ $parlamentar->nome_parlamentar }}</h1>
            <div class="profile-badges">
                @if($parlamentar->filiacaoAtual)
                    <span class="profile-badge">
                        {{ $parlamentar->filiacaoAtual->partido_sigla }} - {{ $parlamentar->filiacaoAtual->partido_nome }}
                    </span>
                @else
                    <span class="profile-badge">Sem partido</span>
                @endif
                <span class="profile-badge">
                    {{ $parlamentar->cidade->nome }} / {{ $parlamentar->cidade->uf }}
                </span>
            </div>
        </div>
    </header>

    <!-- Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value text-blue-600">{{ number_format($totalMaterias, 0, ',', '.') }}</div>
            <div class="stat-label">Matérias apresentadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value text-indigo-600">{{ number_format($totalAcessos, 0, ',', '.') }}</div>
            <div class="stat-label">Acessos ao perfil</div>
        </div>
        <div class="stat-card">
            <div class="stat-value {{ $parlamentar->ativo ? 'status-active' : 'text-red-600' }}">
                {{ $parlamentar->ativo ? 'Ativo' : 'Inativo' }}
            </div>
            <div class="stat-label">Status atual</div>
        </div>
    </div>

    <!-- Matérias recentes -->
    <section>
        <h2 class="section-title">Matérias Recentes Apresentadas</h2>
        <div class="materias-list">
            @forelse($materiasRecentes as $materia)
                <a href="{{ route('materias.show', [$parlamentar->cidade->slug, $materia->id]) }}" class="materia-item block">
                    <div class="materia-title">{{ $materia->ementa }}</div>
                    <div class="materia-meta">
                        <span>{{ $materia->tipo_sigla ?? 'MAT' }}</span>
                        <span>{{ $materia->data_apresentacao?->format('d/m/Y') ?? 'Sem data' }}</span>
                        <span>{{ number_format($materia->acessos, 0, ',', '.') }} acessos</span>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    Nenhuma matéria apresentada recentemente.
                </div>
            @endforelse
        </div>
    </section>

    <!-- Resumo IA futuro -->
    <div class="ia-summary">
        <p class="text-lg font-medium">
            Em breve: Resumo inteligente com IA sobre as principais bandeiras legislativas do parlamentar.
        </p>
    </div>

    <footer>
        © {{ date('Y') }} BuscaLeis — Transparência e inteligência legislativa
    </footer>
</div>
</body>
</html>