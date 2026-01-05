<div class="row">
    @foreach($cidades as $cidade)
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    @if($cidade->brasao)
                        <img src="{{ asset('storage/' . ltrim($cidade->brasao, '/')) }}"
                             class="img-fluid rounded mb-3"
                             style="max-height: 100px; object-fit: contain;"
                             alt="Brasão {{ $cidade->nome }}"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($cidade->nome) }}&background=f3f4f6&color=6b7280'">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3 mx-auto"
                             style="height: 100px; width: 100px;">
                            <span class="text-muted fw-bold">SEM BRASÃO</span>
                        </div>
                    @endif

                    <h5 class="card-title mb-1">{{ $cidade->nome }} / {{ $cidade->uf }}</h5>
                    <p class="text-muted small mb-3">
                        {{ $cidade->parlamentares_count }} parlamentar{{ $cidade->parlamentares_count !== 1 ? 'es' : '' }}
                    </p>

                    <a href="{{ route('platform.parlamentar.list.by.city', $cidade) }}"
                       class="btn btn-primary btn-sm">
                        Ver Parlamentares
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($cidades->isEmpty())
    <div class="alert alert-info text-center">
        Nenhuma cidade indexada ainda.
    </div>
@endif