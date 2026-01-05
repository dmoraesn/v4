<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Parlamentares de {{ $cidade->nome }} — BuscaLeis</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .header .badge {
            background: #10b981;
            color: white;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 600;
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

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 85, 255, 0.1);
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
            transition: background 0.2s;
        }

        .item:hover {
            background: #f8fafc;
        }

        .item:last-child {
            border-bottom: none;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .item-info {
            flex: 1;
        }

        .item a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 16px;
        }

        .item a:hover {
            color: var(--primary);
        }

        .item .partido {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 4px;
            font-weight: 600;
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
        <a href="{{ route('cidade.home', $cidade->slug) }}">← Voltar para {{ $cidade->nome }}</a>
    </div>

    <header class="header">
        <div>
            <h1>Parlamentares de {{ $cidade->nome }}</h1>
            <p>Conheça os parlamentares que propõem e votam as leis do município.</p>
        </div>
        <div class="badge">
            {{ $parlamentaresAtivos }} ativos
        </div>
    </header>

    <div class="search-box">
        <input
            type="text"
            id="busca-parlamentar"
            placeholder="Buscar parlamentar por nome ou partido..."
            autocomplete="off"
        >
    </div>

    {{-- Lista completa de parlamentares ativos --}}
    <section class="section">
        <div class="section-title">
            <h2>Todos os parlamentares ativos</h2>
            <span>base oficial da Câmara • atualizado automaticamente</span>
        </div>

        <div class="list">
            @forelse($parlamentares as $parlamentar)
                @php
                    $slugParlamentar = \Illuminate\Support\Str::slug($parlamentar->nome_parlamentar);
                @endphp

                <div class="item" data-nome="{{ strtolower($parlamentar->nome_parlamentar . ' ' . ($parlamentar->filiacaoAtual?->partido_sigla ?? '')) }}">
                    <div class="avatar">
                        {{ strtoupper(substr($parlamentar->nome_parlamentar, 0, 1)) }}
                    </div>

                    <div class="item-info">
                        <a href="{{ route('parlamentar.show', ['cidade' => $cidade->slug, 'parlamentar' => $slugParlamentar]) }}">
                            {{ $parlamentar->nome_parlamentar }}
                        </a>

                        <div class="partido">
                            @if($parlamentar->filiacaoAtual)
                                {{ $parlamentar->filiacaoAtual->partido_sigla }} - {{ $parlamentar->filiacaoAtual->partido_nome }}
                            @else
                                Sem partido
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty">
                    <strong>Nenhum parlamentar ativo encontrado</strong><br><br>
                    Os dados estão sendo sincronizados. Tente novamente em alguns minutos.
                </div>
            @endforelse
        </div>
    </section>

    <footer>
        © {{ date('Y') }} BuscaLeis — Transparência e inteligência legislativa
    </footer>
</div>

<script>
    // Busca client-side simples e instantânea (inclui partido na busca)
    document.getElementById('busca-parlamentar').addEventListener('input', function(e) {
        const termo = e.target.value.toLowerCase().trim();
        const itens = document.querySelectorAll('.item');

        itens.forEach(item => {
            const texto = item.getAttribute('data-nome');
            if (texto.includes(termo)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>