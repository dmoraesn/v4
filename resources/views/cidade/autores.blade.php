<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Vereadores de {{ $cidade['nome'] }} — BuscaLeis</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

        /* Cabeçalho */
        .header {
            background: var(--card-bg);
            padding: 32px;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 32px;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
        }

        .header p {
            margin: 6px 0 0;
            font-size: 15px;
            color: var(--text-light);
        }

        /* Busca */
        .search-box {
            margin: 24px 0 40px;
        }

        .search-box input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1px solid var(--border);
            font-size: 15px;
            outline: none;
        }

        /* Seções */
        .section {
            margin-bottom: 48px;
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

        .section-title span {
            font-size: 13px;
            color: var(--text-light);
        }

        /* Lista */
        .list {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
        }

        .item {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .item:last-child {
            border-bottom: none;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .item a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 15px;
        }

        .item a:hover {
            color: var(--primary);
        }

        /* Empty state */
        .empty {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-light);
            font-size: 14px;
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
        <a href="{{ route('cidade.home', $cidade['slug']) }}">← Voltar para {{ $cidade['nome'] }}</a>
    </div>

    <header class="header">
        <h1>Vereadores de {{ $cidade['nome'] }}</h1>
        <p>Conheça os parlamentares que propõem e votam as leis do município.</p>
    </header>

    <div class="search-box">
        <input
            type="text"
            placeholder="Buscar vereador por nome (em breve)"
            disabled
        >
    </div>

    {{-- Destaque --}}
    <section class="section">
        <div class="section-title">
            <h2>Vereadores em destaque</h2>
            <span>mais buscados</span>
        </div>

        <div class="list">
            <div class="empty">
                Em breve você verá aqui os vereadores mais acessados e ativos da cidade.
            </div>
        </div>
    </section>

    {{-- Lista completa --}}
    <section class="section">
        <div class="section-title">
            <h2>Todos os vereadores</h2>
            <span>base oficial da Câmara</span>
        </div>

        <div class="list">
            @forelse($autores as $autor)
                <div class="item">
                    <div class="avatar">
                        {{ strtoupper(substr($autor['nome'], 0, 1)) }}
                    </div>
                    <a href="{{ $autor['url'] }}" target="_blank">
                        {{ $autor['nome'] }}
                    </a>
                </div>
          @empty
    <div class="empty">
        <strong>Vereadores em fase de indexação</strong><br><br>

        Estamos organizando e validando os dados dos parlamentares de 
        <strong>{{ $cidade['nome'] }}</strong>.<br><br>

        Em breve, esta página exibirá:
        <ul style="margin: 10px 0 0 18px; text-align: left;">
            <li>Ranking dos vereadores mais atuantes</li>
            <li>Perfis individuais com histórico legislativo</li>
            <li>Matérias e leis associadas a cada parlamentar</li>
        </ul>
    </div>
@endforelse

        </div>
    </section>

    <footer>
        © {{ date('Y') }} BuscaLeis — Transparência e inteligência legislativa
    </footer>

</div>

</body>
</html>
