@if($ativo)
    <span class="badge bg-success text-white fw-bold px-3 py-2" style="font-size: 0.95rem;">
        <i class="bi bi-check-circle-fill me-1"></i> Ativo
    </span>
@else
    <span class="badge bg-danger text-white fw-bold px-3 py-2" style="font-size: 0.95rem;">
        <i class="bi bi-x-circle-fill me-1"></i> Inativo
    </span>
@endif