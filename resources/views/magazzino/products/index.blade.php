<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">Catalogo Prodotti Regionale</h2>
            <a href="{{ route('magazzino.prodotti.create') }}" class="btn btn-primary">
                + Nuovo Prodotto
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- KPI Bar - stile Risorse Umane --}}
            <div class="row g-3 mb-4">
                <x-kpi-card color="blue"   label="Totale Prodotti" :value="$totalProducts" />
                <x-kpi-card color="green"  label="Con Barcode"     :value="\App\Models\WarehouseProduct::whereNotNull('barcode')->count()" />
                <x-kpi-card color="orange" label="Inventariabili"  :value="\App\Models\WarehouseProduct::where('is_inventariable', true)->count()" />
            </div>

            <!-- Search Bar (Scanner Barcode) -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('magazzino.prodotti.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" id="barcodeScanner" class="form-control form-control-lg me-2" placeholder="Scansiona Barcode o digita nome/codice prodotto..." value="{{ request('search') }}" autofocus>
                        <button type="submit" class="btn btn-secondary btn-lg">Cerca</button>
                        <a href="{{ route('magazzino.prodotti.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Reset</a>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Codice</th>
                                    <th>Barcode</th>
                                    <th>Nome Prodotto</th>
                                    <th>Marca</th>
                                    <th>Categoria</th>
                                    <th>U.M.</th>
                                    <th>Tracciato</th>
                                    <th class="text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $p)
                                    <tr>
                                        <td><strong>{{ $p->code ?? '-' }}</strong></td>
                                        <td>
                                            @if($p->barcode)
                                                <span class="badge bg-secondary"><i class="fas fa-barcode"></i> {{ $p->barcode }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $p->name }}</td>
                                        <td>{{ $p->brand ?? '-' }}</td>
                                        <td>{{ $p->category ?? '-' }}</td>
                                        <td>{{ strtoupper($p->unit_of_measure) }}</td>
                                        <td>
                                            @if($p->is_inventariable)
                                                <span class="badge bg-info">Seriale Obbligatorio</span>
                                            @else
                                                <span class="badge bg-light text-dark">Lotto/Quantità</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('magazzino.prodotti.edit', $p) }}" class="btn btn-sm btn-outline-primary">Modifica</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Nessun prodotto trovato.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($products->hasPages())
                    <div class="card-footer bg-white">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <x-back-button :url="route('magazzino.index')" label="← Torna al Magazzino" />

</x-app-layout>
