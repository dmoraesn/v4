<div class="container-fluid p-0">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @foreach($cidades as $cidade)
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-4 text-center p-4 hover-lift">
                    <div class="card-body d-flex flex-column align-items-center">

                        <div class="mb-3 d-flex align-items-center justify-content-center" style="height: 100px; width: 100px;">
                            @if($cidade->brasao_url)
                                <img src="{{ $cidade->brasao_url }}" 
                                     alt="Brasão de {{ $cidade->nome }}" 
                                     class="img-fluid" 
                                     style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="height: 80px; width: 80px;">
                                    <i class="bs.image text-muted" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                        </div>

                        <h3 class="h5 fw-bold text-dark mt-3 mb-1">
                            {{ $cidade->nome }}
                        </h3>

                        <p class="text-muted small mb-4">
                            {{ $cidade->uf }}
                        </p>

                        <div class="mt-auto w-100">
                            <a href="{{ $cidade->sapl }}"
                               target="_blank"
                               class="btn btn-link text-primary fw-bold text-decoration-none d-flex align-items-center justify-content-center gap-2 mb-2">
                                Acessar SAPL
                                <i class="bs.box-arrow-up-right"></i>
                            </a>

                            <a href="{{ route('platform.cidade.edit', $cidade) }}"
                               class="btn btn-light btn-sm w-100 rounded-pill py-2 text-secondary">
                                <i class="bs.pencil-square me-1"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {!! $cidades->links() !!}
    </div>
</div>

<style>
    .hover-lift {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    .rounded-4 { border-radius: 1rem !important; }
</style>