<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">📊 Giacenze & Scorte</h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            {{-- Filters --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('magazzino.stock.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small">Ricerca Prodotto</label>
                            <input type="text" name="search" class="form-control" placeholder="Nome, codice, barcode..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Ubicazione</label>
                            <select name="location_id" class="form-select">
                                <option value="">Tutte le ubicazioni</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="low_stock" id="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}>
                                <label class="form-check-label" for="low_stock">⚠️ Solo Sotto Scorta</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Filtra</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Stock Table --}}
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Prodotto</th>
                                    <th>Codice / Barcode</th>
                                    <th>Ubicazione</th>
                                    <th class="text-center">Quantità</th>
                                    <th class="text-center">Min.</th>
                                    <th class="text-center">Ottimale</th>
                                    <th class="text-center">Stato</th>
                                    <th class="text-center">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $s)
                                    @php
                                        $qty = $s->quantity;
                                        $min = $s->min_stock;
                                        $opt = $s->optimal_stock;
                                        $statusClass = $qty <= 0 ? 'danger' : ($min && $qty < $min ? 'warning' : 'success');
                                        $statusLabel = $qty <= 0 ? '🔴 Esaurito' : ($min && $qty < $min ? '🟡 Sotto Min.' : '🟢 OK');
                                    @endphp
                                    <tr class="{{ $qty <= 0 ? 'table-danger' : ($min && $qty < $min ? 'table-warning' : '') }}">
                                        <td>
                                            <strong>{{ $s->product->name }}</strong><br>
                                            <small class="text-muted">{{ $s->product->category }} · {{ strtoupper($s->product->unit_of_measure) }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $s->product->code ?: '-' }}</small>
                                            @if($s->product->barcode)
                                                <br><span class="badge bg-light text-dark border" style="font-size:0.7em">{{ $s->product->barcode }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $s->location->name }}</td>
                                        <td class="text-center fw-bold fs-5">{{ $qty }}</td>
                                        <td class="text-center text-muted">{{ $min ?? '-' }}</td>
                                        <td class="text-center text-muted">{{ $opt ?? '-' }}</td>
                                        <td class="text-center"><span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                        <td class="text-center">
                                            <a href="{{ route('magazzino.movimenti.create', ['product_id' => $s->product_id, 'location_id' => $s->location_id]) }}" class="btn btn-sm btn-outline-primary">➕ Movimento</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-5">Nessuna giacenza trovata. Carica il primo prodotto tramite un Movimento.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($stocks->hasPages())
                    <div class="card-footer bg-white">{{ $stocks->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
