<x-app-layout>
    <x-slot name="header">
        Strumenti DOS
    </x-slot>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-tree text-success"></i> <i class="fas fa-fire text-danger"></i> Strumenti DOS</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Torna alla Dashboard</a>
        </div>
    </div>

    <div class="row">
        <!-- Strumento Gestione Incendio -->
        <div class="col-xl-3 col-md-6 mb-4 cursor-pointer" onclick="window.location.href='{{ route('dos.fire_management') }}'">
            <div class="card border-left-danger shadow h-100 py-2 hover-zoom" style="transition: transform 0.2s; cursor: pointer;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                App Mobile / Operativo sul campo
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Gestione Incendio</div>
                            <p class="mt-2 text-muted small">Cattura fiamme in Mappa Interattiva • GPS Vento e Distanze • Email SOUP/COP rapida</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Placeholder futuri strumenti -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2" style="opacity: 0.6;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Prossimamente
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Bozza Intervento</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hammer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .hover-zoom:hover { transform: scale(1.02); }
</style>
</style>
</x-app-layout>
