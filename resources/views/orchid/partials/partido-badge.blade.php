@if($parlamentar->filiacaoAtual && $parlamentar->filiacaoAtual->partido_sigla)
    <span class="badge bg-primary text-white fw-bold px-3 py-2" style="font-size: 0.95rem;">
        {{ $parlamentar->filiacaoAtual->partido_sigla }}
    </span>
@else
    <span class="text-muted small fst-italic">
        Sem partido
    </span>
@endif