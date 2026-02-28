<x-app-layout>
    <x-slot name="header">
        Gestione Anagrafica — Personale Interno
    </x-slot>

    <style>
        .emp-table td { vertical-align: middle; font-size: 0.88em; }
        .emp-table th { font-size: 0.85em; white-space: nowrap; }
        .filter-bar { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .bulk-bar { background: #e3f2fd; border-radius: 8px; padding: 12px 15px; margin-bottom: 15px; display: none; }
        .badge-status { font-size: 0.75em; padding: 4px 10px; }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Contatori --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('hr.internal.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
            Tutti <span class="badge bg-light text-dark ms-1">{{ $statusCounts['all'] }}</span>
        </a>
        <a href="{{ route('hr.internal.index', ['status' => 'active']) }}" class="btn btn-sm {{ request('status') === 'active' ? 'btn-success' : 'btn-outline-success' }}">
            Attivi <span class="badge bg-light text-dark ms-1">{{ $statusCounts['active'] }}</span>
        </a>
        <a href="{{ route('hr.internal.index', ['status' => 'suspended']) }}" class="btn btn-sm {{ request('status') === 'suspended' ? 'btn-warning' : 'btn-outline-warning' }}">
            Sospesi <span class="badge bg-light text-dark ms-1">{{ $statusCounts['suspended'] }}</span>
        </a>
        <a href="{{ route('hr.internal.index', ['status' => 'terminated']) }}" class="btn btn-sm {{ request('status') === 'terminated' ? 'btn-secondary' : 'btn-outline-secondary' }}">
            Cessati <span class="badge bg-light text-dark ms-1">{{ $statusCounts['terminated'] }}</span>
        </a>
        <a href="{{ route('hr.internal.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-info' : 'btn-outline-info' }}">
            In attesa <span class="badge bg-light text-dark ms-1">{{ $statusCounts['pending'] }}</span>
        </a>
    </div>

    {{-- Barra filtri --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('hr.internal.index') }}" class="row g-2 align-items-end">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="col-md-4">
                <label class="form-label small fw-bold">Cerca</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Nome, cognome, CF, badge..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Sesso</label>
                <select name="gender" class="form-select form-select-sm">
                    <option value="">Tutti</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Maschio</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Femmina</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Tipo</label>
                <select name="employee_type" class="form-select form-select-sm">
                    <option value="">Tutti</option>
                    <option value="internal" {{ request('employee_type') === 'internal' ? 'selected' : '' }}>Interno</option>
                    <option value="external" {{ request('employee_type') === 'external' ? 'selected' : '' }}>Esterno</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Qualifica AIB</label>
                <select name="is_aib_qualified" class="form-select form-select-sm">
                    <option value="">Tutti</option>
                    <option value="1" {{ request('is_aib_qualified') === '1' ? 'selected' : '' }}>Sì</option>
                    <option value="0" {{ request('is_aib_qualified') === '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">🔍 Filtra</button>
                <a href="{{ route('hr.internal.index') }}" class="btn btn-outline-secondary btn-sm">✕</a>
            </div>
        </form>
    </div>

    {{-- Barra azioni di massa (solo per chi può modificare) --}}
    @if($canEdit)
    <div class="bulk-bar" id="bulkBar">
        <form method="POST" action="{{ route('hr.internal.bulkUpdate') }}" id="bulkForm" class="d-flex gap-2 align-items-center flex-wrap"
              onsubmit="return confirm('Aggiornare i dipendenti selezionati?')">
            @csrf
            <div id="bulkIdsContainer"></div>
            <strong class="me-2"><span id="bulkCount">0</span> selezionati</strong>
            <select name="field" class="form-select form-select-sm" style="width:auto;" required>
                <option value="">— Campo —</option>
                <option value="status">Stato</option>
                <option value="position">Posizione</option>
                <option value="employee_type">Tipo</option>
                <option value="is_aib_qualified">Qualifica AIB</option>
                <option value="is_emergency_available">Disponibilità Emergenza</option>
            </select>
            <input type="text" name="value" class="form-control form-control-sm" style="width:150px;" placeholder="Nuovo valore" required>
            <button type="submit" class="btn btn-warning btn-sm">⚡ Applica a tutti</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAll()">Deseleziona</button>
        </form>
    </div>
    @endif

    {{-- Export --}}
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">{{ $employees->total() }} risultati — Pagina {{ $employees->currentPage() }} di {{ $employees->lastPage() }}</small>
        <a href="{{ route('hr.export', request()->query()) }}" class="btn btn-outline-success btn-sm">📥 Esporta CSV</a>
    </div>

    {{-- Tabella dipendenti --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 emp-table">
                <thead class="table-light">
                    <tr>
                        @if($canEdit)<th style="width:30px;"><input type="checkbox" id="selectAll" title="Seleziona tutti"></th>@endif
                        <th>Cognome</th>
                        <th>Nome</th>
                        <th>Codice Fiscale</th>
                        <th>Data Nascita</th>
                        <th>Sesso</th>
                        <th>Badge</th>
                        <th>Stato</th>
                        <th>AIB</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        @if($canEdit)<td><input type="checkbox" class="emp-check" value="{{ $emp->id }}"></td>@endif
                        <td><strong>{{ $emp->last_name }}</strong></td>
                        <td>{{ $emp->first_name }}</td>
                        <td><code style="font-size:0.85em;">{{ $emp->tax_code }}</code></td>
                        <td>{{ $emp->birth_date ? $emp->birth_date->format('d/m/Y') : '—' }}</td>
                        <td>{{ $emp->gender === 'male' ? 'M' : ($emp->gender === 'female' ? 'F' : '—') }}</td>
                        <td>{{ $emp->badge_number ?? '—' }}</td>
                        <td>
                            @switch($emp->status)
                                @case('active')
                                    <span class="badge bg-success badge-status">Attivo</span>
                                    @break
                                @case('suspended')
                                    <span class="badge bg-warning text-dark badge-status">Sospeso</span>
                                    @break
                                @case('terminated')
                                    <span class="badge bg-secondary badge-status">Cessato</span>
                                    @break
                                @default
                                    <span class="badge bg-info badge-status">In attesa</span>
                            @endswitch
                        </td>
                        <td>
                            @if($emp->is_aib_qualified)
                                <span class="badge bg-danger badge-status">🔥 AIB</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('hr.internal.show', $emp->id) }}" class="btn btn-outline-primary btn-sm" style="font-size:0.8em;">
                                📂 Fascicolo
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Nessun dipendente trovato con i filtri selezionati.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $employees->links() }}</div>

    <div class="mt-4">
        <a href="{{ route('hr.index') }}" class="btn btn-outline-secondary">← Torna a Risorse Umane</a>
    </div>

    @if($canEdit)
    <script>
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.emp-check');
    const bulkBar = document.getElementById('bulkBar');
    const bulkCount = document.getElementById('bulkCount');
    const bulkIdsContainer = document.getElementById('bulkIdsContainer');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.emp-check:checked');
        bulkCount.textContent = checked.length;
        bulkBar.style.display = checked.length > 0 ? 'block' : 'none';

        bulkIdsContainer.innerHTML = '';
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'employee_ids[]';
            input.value = cb.value;
            bulkIdsContainer.appendChild(input);
        });
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkBar();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkBar));

    function deselectAll() {
        selectAll.checked = false;
        checkboxes.forEach(cb => cb.checked = false);
        updateBulkBar();
    }
    </script>
    @endif
</x-app-layout>
