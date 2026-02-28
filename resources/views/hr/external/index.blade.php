<x-app-layout>
    <x-slot name="header">
        Personale Esterno
    </x-slot>

    <style>
        .emp-table td { vertical-align: middle; font-size: 0.88em; }
        .emp-table th { font-size: 0.85em; white-space: nowrap; }
        .filter-bar { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .placeholder-box {
            border: 2px dashed #ccc; border-radius: 12px; padding: 30px;
            text-align: center; color: #999; background: #fafafa;
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Collaboratori Esterni</h4>
        @if($canEdit)
            <a href="{{ route('hr.external.create') }}" class="btn btn-success">
                ➕ Nuovo Collaboratore
            </a>
        @endif
    </div>

    {{-- Barra filtri --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('hr.external.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Cerca</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Nome, cognome, codice fiscale..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Organizzazione</label>
                <select name="organization_id" class="form-select form-select-sm">
                    <option value="">Tutte</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Qualifica AIB</label>
                <select name="is_aib" class="form-select form-select-sm">
                    <option value="">Tutti</option>
                    <option value="1" {{ request('is_aib') === '1' ? 'selected' : '' }}>Qualificato AIB</option>
                    <option value="0" {{ request('is_aib') === '0' ? 'selected' : '' }}>Non qualificato</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">🔍 Filtra</button>
                <a href="{{ route('hr.external.index') }}" class="btn btn-outline-secondary btn-sm">✕</a>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">{{ $employees->total() }} collaboratori esterni</small>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 emp-table">
                <thead class="table-light">
                    <tr>
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Codice Fiscale</th>
                        <th>Organizzazione</th>
                        <th>AIB</th>
                        <th>Mansione</th>
                        <th>Inizio</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td><strong>{{ $emp->last_name }}</strong></td>
                        <td>{{ $emp->first_name }}</td>
                        <td><code style="font-size:0.85em;">{{ $emp->tax_code }}</code></td>
                        <td>
                            @if($emp->organization)
                                <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $emp->organization->name }}">
                                    {{ $emp->organization->name }}
                                </span>
                            @else
                                <span class="text-muted small">Nessuna</span>
                            @endif
                        </td>
                        <td>
                            @if($emp->is_aib)
                                <span class="badge bg-danger" title="Antincendio Boschivo">AIB</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $emp->job_title ?? '—' }}</td>
                        <td>{{ $emp->start_date ? \Carbon\Carbon::parse($emp->start_date)->format('d/m/Y') : '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('hr.external.show', $emp->id) }}" class="btn btn-outline-primary btn-sm" style="font-size:0.8em;">
                                📂 Dettaglio
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Nessun collaboratore esterno registrato.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $employees->links() }}</div>

    {{-- Spazio per future integrazioni --}}
    <div class="mt-4">
        <div class="placeholder-box">
            <div style="font-size: 1.5em; margin-bottom: 8px;">🔧</div>
            <p class="mb-1 fw-bold">Spazio per future integrazioni</p>
            <p class="mb-0 small">Qui verranno aggiunte ulteriori funzionalità per la gestione del personale esterno:<br>
            importazione massiva, contratti, presenze, documentazione.</p>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('hr.index') }}" class="btn btn-outline-secondary">← Torna a Risorse Umane</a>
    </div>
</x-app-layout>
