<div class="d-flex align-items-center justify-content-center rounded-circle bg-light border border-2 border-white shadow-sm"
     style="width: 100px; height: 100px; overflow: hidden;">

    @if($cidade->brasao_url)
        <img src="{{ $cidade->brasao_url }}"
             alt="Brasão de {{ $cidade->nome }}"
             class="img-fluid p-2"
             style="max-height: 100%; width: auto; object-fit: contain;"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        
        <div class="flex-column align-items-center justify-content-center text-muted opacity-50" style="display: none;">
            <span style="font-size: 1.5rem;">⚠️</span>
            <span class="fw-bold" style="font-size: 0.6rem;">ERRO</span>
        </div>
    @else
        <div class="d-flex flex-column align-items-center justify-content-center text-muted opacity-50">
            <span style="font-size: 2rem;">🏛️</span>
            <span class="fw-bold" style="font-size: 0.7rem;">SEM IMAGEM</span>
        </div>
    @endif
</div>