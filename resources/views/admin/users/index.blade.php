<x-app-layout>
    <x-slot name="header">
        Gestione Utenti
    </x-slot>

    <style>
        .user-table .btn-cell,
        .user-table .badge-cell {
            font-size: 0.78em !important;
            font-weight: 400 !important;
            padding: 5px 12px !important;
            min-width: 80px;
            text-align: center;
            display: inline-block;
            line-height: 1.4;
            border-radius: 4px;
            vertical-align: middle;
            border: 1px solid transparent;
            box-sizing: border-box;
            height: 28px;
        }
        .user-table .btn-cell-status,
        .user-table .badge-cell-status {
            min-width: 90px;
        }
        .user-table td {
            vertical-align: middle;
            font-size: 0.9em;
        }
        .user-table .btn-action {
            font-size: 0.8em;
            font-weight: 400;
            padding: 3px 10px;
            vertical-align: middle;
        }
        .user-table .btn-trash {
            font-size: 1em;
            padding: 2px 8px;
            vertical-align: middle;
            border: none;
            background: none;
            color: #dc3545;
            cursor: pointer;
            opacity: 0.7;
        }
        .user-table .btn-trash:hover { opacity: 1; }
        /* Search results */
        .emp-result { cursor: pointer; padding: 10px 15px; border-bottom: 1px solid #eee; transition: background 0.15s; }
        .emp-result:hover { background: #f0f4ff; }
        .emp-result.disabled { opacity: 0.5; cursor: not-allowed; }
        .emp-result .badge { font-size: 0.7em; }
        /* Detail form */
        .detail-field { font-size: 0.9em; }
        .detail-field label { font-weight: 600; color: #555; font-size: 0.85em; }
        /* Extra role rows */
        .extra-role-row { display: flex; gap: 8px; margin-bottom: 8px; align-items: center; }
        .extra-role-row select { flex: 1; }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header con pulsante Aggiungi -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.users.index') }}" 
               class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                Tutti <span class="badge bg-light text-dark ms-1">{{ $counts['all'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'pending']) }}" 
               class="btn btn-sm {{ $filter === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                In attesa <span class="badge bg-light text-dark ms-1">{{ $counts['pending'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'active']) }}" 
               class="btn btn-sm {{ $filter === 'active' ? 'btn-success' : 'btn-outline-success' }}">
                Attivi <span class="badge bg-light text-dark ms-1">{{ $counts['active'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'suspended']) }}" 
               class="btn btn-sm {{ $filter === 'suspended' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                Disattivati <span class="badge bg-light text-dark ms-1">{{ $counts['suspended'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'norole']) }}" 
               class="btn btn-sm {{ $filter === 'norole' ? 'btn-danger' : 'btn-outline-danger' }}">
                Senza ruolo <span class="badge bg-light text-dark ms-1">{{ $counts['norole'] }}</span>
            </a>
            <a href="{{ route('admin.users.index', ['filter' => 'rejected']) }}" 
               class="btn btn-sm {{ $filter === 'rejected' ? 'btn-dark' : 'btn-outline-dark' }}">
                🚫 Rifiutati/Eliminati <span class="badge bg-light text-dark ms-1">{{ $counts['rejected'] }}</span>
            </a>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#searchEmployeeModal">
            ➕ Aggiungi Utente
        </button>
    </div>

    @if($filter === 'rejected')
    {{-- ======================== TABELLA RIFIUTATI ======================== --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 user-table">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Codice Fiscale</th>
                        <th>Rifiutato il</th>
                        <th>Da</th>
                        <th>Motivo</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rejected as $rej)
                    <tr>
                        <td><strong>{{ $rej->surname }}</strong> {{ $rej->name }}</td>
                        <td>{{ $rej->email }}</td>
                        <td><code>{{ $rej->fiscal_code }}</code></td>
                        <td>{{ \Carbon\Carbon::parse($rej->rejected_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            @php $admin = \App\Models\User::find($rej->rejected_by); @endphp
                            {{ $admin ? $admin->name . ' ' . $admin->surname : 'N/D' }}
                        </td>
                        <td class="text-muted">{{ $rej->rejection_reason ?? '—' }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.users.reintegrate', $rej->id) }}" 
                                  class="d-inline" onsubmit="return confirm('Reintegrare {{ $rej->name }} {{ $rej->surname }}?\nVerrà ri-creato come utente attivo.')">
                                @csrf
                                <button type="submit" class="btn btn-success btn-action">♻️ Reintegra</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Nessun utente rifiutato o eliminato.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($rejected, 'links'))
    <div class="mt-3">{{ $rejected->appends(['filter' => $filter])->links() }}</div>
    @endif

    @else
    {{-- ======================== TABELLA UTENTI ======================== --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 user-table">
                <thead class="table-light">
                    <tr>
                        <th style="width:30px;"></th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>CF</th>
                        <th>Tipo</th>
                        <th>Stato</th>
                        <th style="min-width: 150px;">Aree e Privilegi (1-5)</th>
                        <th>Ruolo Spec.</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php $isSuperAdmin = $user->hasRole('super-admin'); @endphp
                    <tr class="{{ $user->status === 'suspended' ? 'table-secondary' : '' }}">
                        <td class="text-center" style="padding:0;">
                            @if(!$isSuperAdmin)
                                <button type="button" class="btn-trash" title="Elimina e archivia"
                                        onclick="document.getElementById('delete-{{ $user->id }}').style.display='block'">🗑</button>
                            @endif
                        </td>
                        <td><strong>{{ $user->surname }}</strong> {{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><code>{{ $user->fiscal_code }}</code></td>
                        <td>
                            @if(!$isSuperAdmin)
                                <form method="POST" action="{{ route('admin.users.toggleType', $user->id) }}" class="d-inline"
                                      onsubmit="return confirm('Cambiare tipo di {{ $user->name }} {{ $user->surname }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-cell {{ $user->type === 'internal' ? 'btn-primary' : 'btn-info' }}" title="Clicca per cambiare">
                                        {{ $user->type === 'internal' ? 'Interno' : 'Esterno' }}
                                    </button>
                                </form>
                            @else
                                <span class="badge-cell {{ $user->type === 'internal' ? 'bg-primary text-white' : 'bg-info text-white' }}">
                                    {{ $user->type === 'internal' ? 'Interno' : 'Esterno' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'pending')
                                <span class="badge-cell badge-cell-status bg-warning text-dark">In attesa</span>
                            @elseif(!$isSuperAdmin)
                                <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}" class="d-inline"
                                      onsubmit="return confirm('{{ $user->status === 'active' ? 'Disattivare' : 'Riattivare' }} {{ $user->name }} {{ $user->surname }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-cell btn-cell-status {{ $user->status === 'active' ? 'btn-success' : 'btn-secondary' }}" 
                                            title="Clicca per {{ $user->status === 'active' ? 'disattivare' : 'riattivare' }}">
                                        {{ $user->status === 'active' ? '✓ Attivo' : '⏸ Disattivato' }}
                                    </button>
                                </form>
                            @else
                                <span class="badge-cell badge-cell-status bg-success text-white">✓ Attivo</span>
                            @endif
                        </td>
                        <td>
                            @forelse($user->areaRoles as $ar)
                                @php
                                    $lvlColors = [1 => '#198754', 2 => '#20c997', 3 => '#ffc107', 4 => '#fd7e14', 5 => '#dc3545'];
                                    $lc = $lvlColors[$ar->privilege_level] ?? '#6c757d';
                                @endphp
                                <div class="mb-1 d-flex align-items-center gap-1">
                                    <span class="badge" style="background-color: #f8f9fa; color: #333; border: 1px solid #ddd; font-size: 0.7em; padding: 2px 6px;">
                                        {{ $ar->area }}
                                    </span>
                                    <span class="badge text-white shadow-sm" style="background-color: {{ $lc }}; font-size: 0.65em; padding: 2px 6px;">
                                        L{{ $ar->privilege_level }}
                                    </span>
                                    <form method="POST" action="{{ route('admin.users.removeAreaRole', $ar->id) }}" class="d-inline" onsubmit="return confirm('Rimuovere abilitazione {{ $ar->area }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-0 border-0 bg-transparent text-danger ms-1" style="font-size: 0.8em; line-height: 1;" title="Rimuovi">×</button>
                                    </form>
                                </div>
                            @empty
                                <span class="text-muted small">Nessuna area</span>
                            @endforelse
                        </td>
                        <td>
                            @if($user->getRoleNames()->isNotEmpty())
                                @foreach($user->getRoleNames() as $role)
                                    <span class="badge bg-dark" style="font-size:0.75em;">{{ $role }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end flex-wrap align-items-center">
                                @if($user->status === 'pending')
                                    <form method="POST" action="{{ route('admin.users.approve', $user->id) }}" class="d-inline"
                                          onsubmit="return confirm('Approvare {{ $user->name }} {{ $user->surname }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-action">✓</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-action" 
                                            onclick="document.getElementById('reject-{{ $user->id }}').style.display='block'">✗</button>
                                @endif
                                @if($user->status === 'active' || $user->status === 'suspended')
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-outline-secondary btn-action dropdown-toggle" data-bs-toggle="dropdown">Ruolo</button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @foreach($roles as $role)
                                            <li>
                                                <form method="POST" action="{{ route('admin.users.assignRole', $user->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="role" value="{{ $role->name }}">
                                                    <button type="submit" class="dropdown-item" style="color:#333;">
                                                        {{ $user->hasRole($role->name) ? '✓ ' : '' }}{{ ucfirst($role->name) }}
                                                    </button>
                                                </form>
                                            </li>
                                            @endforeach
                                            @if($user->getRoleNames()->isNotEmpty())
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.users.removeRole', $user->id) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item" style="color:#d32f2f;">Rimuovi ruoli</button>
                                                </form>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            @if($user->status === 'pending')
                            <div id="reject-{{ $user->id }}" style="display:none;" class="mt-2 text-start">
                                <form method="POST" action="{{ route('admin.users.reject', $user->id) }}"
                                      onsubmit="return confirm('Rifiutare e archiviare {{ $user->name }} {{ $user->surname }}?')">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="reason" placeholder="Motivo (opzionale)">
                                        <button type="submit" class="btn btn-danger btn-sm">Rifiuta</button>
                                    </div>
                                </form>
                            </div>
                            @endif

                            @if(!$isSuperAdmin)
                            <div id="delete-{{ $user->id }}" style="display:none;" class="mt-2 text-start">
                                <form method="POST" action="{{ route('admin.users.delete', $user->id) }}"
                                      onsubmit="return confirm('Eliminare {{ $user->name }} {{ $user->surname }}?')">
                                    @csrf @method('DELETE')
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" name="reason" placeholder="Motivo (opzionale)">
                                        <button type="submit" class="btn btn-danger btn-sm">Elimina</button>
                                    </div>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Nessun utente trovato per questo filtro.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $users->appends(['filter' => $filter])->links() }}</div>
    @endif

    <div class="mt-4">
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">← Torna all'Amministrazione</a>
    </div>

    {{-- ======================== MODAL 1: CERCA DIPENDENTE ======================== --}}
    <div class="modal fade" id="searchEmployeeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Cerca Dipendente nell'Anagrafica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" id="empSearchInput" class="form-control" 
                               placeholder="Cerca per nome, cognome o codice fiscale..." autofocus>
                        <small class="text-muted">Digita almeno 2 caratteri per cercare</small>
                    </div>
                    <div id="empSearchResults" style="max-height: 400px; overflow-y: auto;">
                        <p class="text-center text-muted py-4">Inserisci un termine di ricerca</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================== MODAL 2: DETTAGLIO E CREAZIONE ======================== --}}
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Crea Utente da Anagrafica</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.users.createFromEmployee') }}" id="createUserForm"
                          onsubmit="return confirm('Creare questo utente con le impostazioni selezionate?')">
                        @csrf
                        <input type="hidden" name="employee_id" id="empId">
                        <input type="hidden" name="employee_type" id="empType">

                        {{-- Info dipendente --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light"><strong>📋 Dati Dipendente</strong></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 detail-field mb-2">
                                        <label>Nome</label>
                                        <div id="detFirstName" class="fw-bold">—</div>
                                    </div>
                                    <div class="col-md-6 detail-field mb-2">
                                        <label>Cognome</label>
                                        <div id="detLastName" class="fw-bold">—</div>
                                    </div>
                                    <div class="col-md-4 detail-field mb-2">
                                        <label>Codice Fiscale</label>
                                        <div id="detTaxCode">—</div>
                                    </div>
                                    <div class="col-md-4 detail-field mb-2">
                                        <label>Data di Nascita</label>
                                        <div id="detBirthDate">—</div>
                                    </div>
                                    <div class="col-md-4 detail-field mb-2">
                                        <label>Tipo</label>
                                        <div id="detType">—</div>
                                    </div>
                                    <div class="col-md-6 detail-field mb-2">
                                        <label>Email Aziendale</label>
                                        <div id="detEmail">—</div>
                                    </div>
                                    <div class="col-md-6 detail-field mb-2">
                                        <label>Posizione / Mansione</label>
                                        <div id="detPosition">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Impostazioni accesso --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light"><strong>🔐 Impostazioni Accesso</strong></div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email di accesso *</label>
                                        <input type="email" name="email" id="empEmailInput" class="form-control" required
                                               placeholder="email@calabriaverde.eu">
                                        <small class="text-muted">Una password sicura verrà generata automaticamente e inviata via email</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Stato iniziale *</label>
                                        <select name="status" class="form-select" required>
                                            <option value="active">✓ Attivo</option>
                                            <option value="suspended">⏸ Disattivato</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Ruolo e area principale --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light"><strong>🎯 Ruolo e Area di Competenza</strong></div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Ruolo di partenza *</label>
                                        <select name="role" class="form-select" required>
                                            <option value="">— Seleziona ruolo —</option>
                                            @foreach($roles ?? [] as $role)
                                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Area di competenza *</label>
                                        <select name="area" class="form-select" required>
                                            <option value="">— Seleziona area —</option>
                                            @foreach($areas ?? [] as $area)
                                                <option value="{{ $area }}">{{ $area }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Livello Privilegio *</label>
                                        <select name="privilege_level" class="form-select" required>
                                            <option value="1">1 - Pieno Controllo</option>
                                            <option value="2">2 - Controllo Parziale</option>
                                            <option value="3">3 - Controllo Campi</option>
                                            <option value="4" selected>4 - Sola Lettura (Full)</option>
                                            <option value="5">5 - Sola Lettura (Parziale)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Ruoli/aree aggiuntivi --}}
                                <div class="border-top pt-3 mt-2">
                                    <label class="form-label text-muted mb-2">Ruoli e Aree aggiuntivi (opzionale)</label>
                                    <div id="extraRolesContainer"></div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="addExtraRole()">
                                        ➕ Aggiungi altro ruolo/area
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="backToSearch()">← Torna alla ricerca</button>
                            <button type="submit" class="btn btn-success">✓ Crea Utente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Areas and Roles data for JS
    const availableAreas = @json($areas ?? []);
    const availableRoles = @json(($roles ?? collect())->pluck('name'));
    let extraRoleCount = 0;

    // Search employee
    let searchTimeout;
    document.getElementById('empSearchInput').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        const container = document.getElementById('empSearchResults');

        if (q.length < 2) {
            container.innerHTML = '<p class="text-center text-muted py-4">Digita almeno 2 caratteri</p>';
            return;
        }

        container.innerHTML = '<p class="text-center py-4"><span class="spinner-border spinner-border-sm"></span> Ricerca...</p>';

        searchTimeout = setTimeout(() => {
            fetch('{{ route("admin.users.searchEmployees") }}?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(results => {
                    if (results.length === 0) {
                        container.innerHTML = '<p class="text-center text-muted py-4">Nessun risultato trovato</p>';
                        return;
                    }
                    let html = '';
                    results.forEach(emp => {
                        const typeLabel = emp.type === 'internal' ? '<span class="badge bg-primary">Interno</span>' : '<span class="badge bg-info">Esterno</span>';
                        const alreadyLabel = emp.already_user ? '<span class="badge bg-warning text-dark ms-2">Già utente</span>' : '';
                        const disabled = emp.already_user ? 'disabled' : '';
                        const clickHandler = emp.already_user ? '' : `onclick="selectEmployee(${emp.id}, '${emp.type}')"`;

                        html += `<div class="emp-result ${disabled}" ${clickHandler}>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${emp.last_name}</strong> ${emp.first_name}
                                    ${typeLabel} ${alreadyLabel}
                                </div>
                                <div>
                                    <code style="font-size:0.85em;">${emp.tax_code || '—'}</code>
                                </div>
                            </div>
                            ${emp.email ? '<small class="text-muted">' + emp.email + '</small>' : ''}
                        </div>`;
                    });
                    container.innerHTML = html;
                })
                .catch(() => {
                    container.innerHTML = '<p class="text-center text-danger py-4">Errore di connessione</p>';
                });
        }, 300);
    });

    // Select employee and load detail
    function selectEmployee(id, type) {
        // Close search modal, open create modal
        const searchModal = bootstrap.Modal.getInstance(document.getElementById('searchEmployeeModal'));
        searchModal.hide();

        fetch(`{{ url('admin/users/employee-detail') }}/${type}/${id}`)
            .then(r => r.json())
            .then(emp => {
                document.getElementById('empId').value = emp.id;
                document.getElementById('empType').value = emp.type;
                document.getElementById('detFirstName').textContent = emp.first_name;
                document.getElementById('detLastName').textContent = emp.last_name;
                document.getElementById('detTaxCode').textContent = emp.tax_code || '—';
                document.getElementById('detBirthDate').textContent = emp.birth_date || '—';
                document.getElementById('detType').innerHTML = emp.type === 'internal' 
                    ? '<span class="badge bg-primary">Interno</span>' 
                    : '<span class="badge bg-info">Esterno</span>';
                document.getElementById('detEmail').textContent = emp.email || emp.personal_email || '—';
                document.getElementById('detPosition').textContent = emp.position || emp.job_title || '—';

                // Pre-fill email
                const emailInput = document.getElementById('empEmailInput');
                emailInput.value = emp.email || emp.personal_email || '';

                // Reset extra roles
                document.getElementById('extraRolesContainer').innerHTML = '';
                extraRoleCount = 0;

                // Open create modal
                const createModal = new bootstrap.Modal(document.getElementById('createUserModal'));
                createModal.show();
            })
            .catch(() => alert('Errore nel caricamento dei dettagli.'));
    }

    function backToSearch() {
        const createModal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
        createModal.hide();
        setTimeout(() => {
            const searchModal = new bootstrap.Modal(document.getElementById('searchEmployeeModal'));
            searchModal.show();
        }, 300);
    }

    function addExtraRole() {
        extraRoleCount++;
        const container = document.getElementById('extraRolesContainer');

        let roleOptions = '<option value="">— Ruolo —</option>';
        availableRoles.forEach(r => { roleOptions += `<option value="${r}">${r.charAt(0).toUpperCase() + r.slice(1)}</option>`; });

        let areaOptions = '<option value="">— Area —</option>';
        availableAreas.forEach(a => { areaOptions += `<option value="${a}">${a}</option>`; });

        const row = document.createElement('div');
        row.className = 'extra-role-row';
        row.id = 'extraRole-' + extraRoleCount;
        row.innerHTML = `
            <select name="extra_roles[${extraRoleCount}][role]" class="form-select form-select-sm">${roleOptions}</select>
            <select name="extra_roles[${extraRoleCount}][area]" class="form-select form-select-sm">${areaOptions}</select>
            <select name="extra_roles[${extraRoleCount}][privilege_level]" class="form-select form-select-sm">
                <option value="1">Livello 1</option>
                <option value="2">Livello 2</option>
                <option value="3">Livello 3</option>
                <option value="4" selected>Livello 4</option>
                <option value="5">Livello 5</option>
            </select>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">✗</button>
        `;
        container.appendChild(row);
    }
    </script>
</x-app-layout>
