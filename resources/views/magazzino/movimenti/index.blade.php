<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">🔁 Movimenti Magazzino</h2>
            <a href="{{ route('magazzino.movimenti.create') }}" class="btn btn-primary">+ Nuovo Movimento</a>
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
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small">Tipo Movimento</label>
                            <select name="tipo" class="form-select">
                                <option value="">Tutti</option>
                                @foreach(['CARICO','SCARICO','TRASFERIMENTO','SMISTAMENTO','ASSEGNAZIONE','RITORNO','INVENTARIO'] as $t)
                                    <option value="{{ $t }}" {{ request('tipo') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Data Da</label>
                            <input type="date" name="da" class="form-control" value="{{ request('da') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Data A</label>
                            <input type="date" name="a" class="form-control" value="{{ request('a') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-secondary w-100">Filtra</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Movements Table --}}
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Data / Ora</th>
                                    <th>Tipo</th>
                                    <th>Prodotto</th>
                                    <th class="text-center">Qtà</th>
                                    <th>Origine</th>
                                    <th>Destinazione</th>
                                    <th>Operatore</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $mv)
                                    @php
                                        $colors = [
                                            'CARICO'       => 'success',
                                            'SCARICO'      => 'danger',
                                            'TRASFERIMENTO'=> 'info',
                                            'SMISTAMENTO'  => 'primary',
                                            'ASSEGNAZIONE' => 'warning',
                                            'RITORNO'      => 'secondary',
                                            'INVENTARIO'   => 'dark',
                                        ];
                                        $color = $colors[$mv->movement_type] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td><small>{{ $mv->movement_date->format('d/m/Y') }}<br>{{ $mv->movement_date->format('H:i') }}</small></td>
                                        <td><span class="badge bg-{{ $color }}">{{ $mv->movement_type }}</span></td>
                                        <td>
                                            <strong>{{ $mv->product->name ?? 'N/D' }}</strong>
                                        </td>
                                        <td class="text-center fw-bold">{{ $mv->quantity }}</td>
                                        <td><small>{{ $mv->sourceLocation->name ?? '-' }}</small></td>
                                        <td><small>{{ $mv->destinationLocation->name ?? '-' }}</small></td>
                                        <td><small>{{ $mv->user->name ?? '-' }} {{ optional($mv->user)->surname }}</small></td>
                                        <td><small class="text-muted">{{ Str::limit($mv->notes, 50) }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-5">Nessun movimento registrato.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($movements->hasPages())
                    <div class="card-footer bg-white">{{ $movements->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
