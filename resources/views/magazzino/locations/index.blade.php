<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">🗺️ Ubicazioni & Struttura Logistica</h2>
            <a href="{{ route('magazzino.locations.create') }}" class="btn btn-primary">+ Nuova Ubicazione</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm border-start border-primary border-4 mb-2">
                        <div class="card-body py-2 px-3">
                            <small class="text-muted text-uppercase">Totale Ubicazioni</small>
                            <h4 class="mb-0">{{ $total }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Legend --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body py-2 px-3 d-flex flex-wrap gap-2 align-items-center">
                    <small class="text-muted fw-bold me-2">Legenda:</small>
                    <span class="badge" style="background:#1565c0">🏛️ Sede Centrale</span>
                    <span class="badge" style="background:#2e7d32">🏢 HUB</span>
                    <span class="badge" style="background:#f57c00">📦 Magazzino</span>
                    <span class="badge" style="background:#6a1b9a">🌿 Distretto</span>
                    <span class="badge" style="background:#d32f2f">🏕️ Distaccamento</span>
                    <span class="badge bg-secondary">📌 Punto Consumo</span>
                </div>
            </div>

            {{-- Tree --}}
            @if($roots->isEmpty())
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5 text-muted">
                        <div class="fs-1 mb-3">🏗️</div>
                        <h5>Albero Logistico Vuoto</h5>
                        <p>Inizia aggiungendo la Sede Centrale o il primo Distretto.</p>
                        <a href="{{ route('magazzino.locations.create') }}" class="btn btn-primary">+ Aggiungi Prima Ubicazione</a>
                    </div>
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($roots as $root)
                                @include('magazzino.locations._node', ['location' => $root, 'depth' => 0])
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
