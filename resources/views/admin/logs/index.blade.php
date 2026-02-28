<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Log Attività di Sistema') }}
            </h2>
            <div class="d-flex gap-2">
                <button type="button" onclick="printLogs()" class="btn btn-outline-primary btn-sm rounded-pill">
                    <i class="fas fa-print me-1"></i> Stampa
                </button>
                <button type="button" onclick="exportLogs()" class="btn btn-outline-success btn-sm rounded-pill">
                    <i class="fas fa-file-export me-1"></i> Esporta CSV
                </button>
                <span class="badge bg-primary px-3 py-2 d-flex align-items-center" style="border-radius: 20px;">
                    <i class="fas fa-shield-alt me-1"></i> Amministratore
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-0">
                <div class="p-6 text-gray-900">
                    
                    {{-- Filtri Avanzati --}}
                    <div class="mb-4 p-4 bg-white rounded shadow-sm border">
                        <form action="{{ route('admin.logs.index') }}" method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted">Ricerca Libera</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Nome, email, dettagli..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">Azione</label>
                                <select name="action" class="form-select form-select-sm">
                                    <option value="">Tutte le azioni</option>
                                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Creazione</option>
                                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Modifica</option>
                                    <option value="view" {{ request('action') == 'view' ? 'selected' : '' }}>Visualizzazione</option>
                                    <option value="security" {{ request('action') == 'security' ? 'selected' : '' }}>Sicurezza</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">Da Data</label>
                                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted">A Data</label>
                                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-filter me-1"></i> Applica
                                </button>
                                <a href="{{ route('admin.logs.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- Toolbar Azioni Multiple --}}
                    <div id="selection-toolbar" class="mb-3 p-2 bg-light rounded d-none justify-content-between align-items-center">
                        <span class="small fw-bold text-primary"><i class="fas fa-check-square me-2"></i><span id="selected-count">0</span> righe selezionate</span>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="printSelected()" class="btn btn-sm btn-outline-primary">Stampa Selezionati</button>
                            <button type="button" onclick="exportSelected()" class="btn btn-sm btn-outline-success">Esporta Selezionati</button>
                        </div>
                    </div>

                    {{-- Tabella Log --}}
                    <div class="table-responsive shadow-sm rounded border">
                        <table class="table table-hover align-middle mb-0" id="logs-table">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th style="width: 40px;" class="text-center no-print">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                    </th>
                                    <th style="width: 160px;">Data e Ora</th>
                                    <th style="width: 180px;">Utente</th>
                                    <th style="width: 100px;">Azione</th>
                                    <th>Descrizione Attività</th>
                                    <th style="width: 120px;">Indirizzo IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr class="cursor-pointer" onclick="showLogDetails({{ $log->id }})">
                                        <td class="text-center no-print" onclick="event.stopPropagation()">
                                            <input type="checkbox" class="form-check-input row-select" value="{{ $log->id }}" data-log='@json($log)'>
                                        </td>
                                        <td class="small fw-bold">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if($log->user)
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.75em;">
                                                        {{ strtoupper(substr($log->user->name, 0, 1) . substr($log->user->surname, 0, 1)) }}
                                                    </div>
                                                    <div class="small">
                                                        <div class="fw-bold">{{ $log->user->name }} {{ $log->user->surname }}</div>
                                                        <div class="text-muted" style="font-size: 0.85em;">{{ $log->user->email }}</div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">Sistema</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($log->action) {
                                                    'login' => 'bg-success',
                                                    'logout' => 'bg-secondary',
                                                    'create' => 'bg-info text-dark',
                                                    'update' => 'bg-warning text-dark',
                                                    'view' => 'bg-light text-dark border',
                                                    'security' => 'bg-danger',
                                                    default => 'bg-primary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} text-uppercase" style="font-size: 0.65em; padding: 4px 8px;">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small text-truncate" style="max-width: 400px;" title="{{ $log->details }}">
                                                {{ $log->details }}
                                            </div>
                                        </td>
                                        <td>
                                            <code class="small text-dark">{{ $log->ip_address == '::1' ? '127.0.0.1 (Locale)' : $log->ip_address }}</code>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-5 text-center text-muted">
                                            <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                            <p>Nessun log trovato per i criteri selezionati.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->appends(request()->all())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Dettaglio --}}
    <div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i> Dettaglio Log Attività</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="log-detail-content">
                    <!-- Dinamico -->
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                    <button type="button" onclick="window.print()" class="btn btn-primary d-none d-print-inline-block">Stampa</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cursor-pointer { cursor: pointer; }
        .x-small { font-size: 0.75rem; }
        @media print {
            .no-print, .btn, .py-12, .mb-4, .input-group, .pagination, footer, header { display: none !important; }
            .modal-dialog { max-width: 100% !important; margin: 0 !important; }
            .modal-content { border: none !important; box-shadow: none !important; }
            .table-responsive { overflow: visible !important; border: none !important; }
            .table { width: 100% !important; font-size: 10pt !important; border-collapse: collapse !important; }
            .table th, .table td { border: 1px solid #ddd !important; padding: 8px !important; }
            .badge { border: 1px solid #333 !important; color: #333 !important; background: none !important; }
            .small.text-truncate { text-wrap: wrap !important; max-width: none !important; overflow: visible !important; }
        }
    </style>

    <script>
        const logData = {
            @foreach($logs as $log)
                "{{ $log->id }}": {
                    ...{!! json_encode($log) !!}, 
                    user_fullName: "{{ $log->user ? $log->user->name . ' ' . $log->user->surname : 'Sistema' }}",
                    user_email: "{{ $log->user ? $log->user->email : '' }}"
                },
            @endforeach
        };

        function showLogDetails(id) {
            const data = logData[id];
            if (!data) return;

            let detailsHtml = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">Data e Ora</label>
                        <div class="fw-bold">${new Date(data.created_at).toLocaleString('it-IT')}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">Azione</label>
                        <div><span class="badge bg-primary text-uppercase">${data.action}</span></div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">Utente</label>
                        <div class="p-2 bg-light rounded border">
                            <strong>${data.user_fullName}</strong> <br>
                            <span class="text-muted small">${data.user_email || 'Nessuna email'}</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">Descrizione Completa</label>
                        <div class="p-3 bg-light rounded border font-monospace small" style="white-space: pre-wrap; word-break: break-all;">${data.details}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">Modulo Interessato</label>
                        <div><code>${data.model || 'N/D'}</code></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">ID Record</label>
                        <div><code>${data.model_id || 'N/D'}</code></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">Indirizzo IP</label>
                        <div><code>${data.ip_address}</code></div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">Browser / User Agent</label>
                        <div class="small text-muted">${data.user_agent}</div>
                    </div>
                </div>
            `;
            
            document.getElementById('log-detail-content').innerHTML = detailsHtml;
            new bootstrap.Modal(document.getElementById('logDetailModal')).show();
        }

        // Selezione Righe
        const selectAll = document.getElementById('select-all');
        const rowSelects = document.querySelectorAll('.row-select');
        const toolbar = document.getElementById('selection-toolbar');
        const countSpan = document.getElementById('selected-count');

        function updateToolbar() {
            const checked = document.querySelectorAll('.row-select:checked');
            countSpan.textContent = checked.length;
            if (checked.length > 0) {
                toolbar.classList.remove('d-none');
                toolbar.classList.add('d-flex');
            } else {
                toolbar.classList.remove('d-flex');
                toolbar.classList.add('d-none');
            }
        }

        selectAll.addEventListener('change', () => {
            rowSelects.forEach(cb => cb.checked = selectAll.checked);
            updateToolbar();
        });

        rowSelects.forEach(cb => {
            cb.addEventListener('change', updateToolbar);
        });

        // Export/Print
        function getSelectedData() {
            const checked = document.querySelectorAll('.row-select:checked');
            return Array.from(checked).map(cb => logData[cb.value]);
        }

        function exportLogs() {
            // Get all items from the current view
            const data = Object.values(logData);
            downloadCSV(data, "log_attivita_vista_corrente.csv");
        }

        function exportSelected() {
            const data = getSelectedData();
            downloadCSV(data, "log_selezionati.csv");
        }

        function downloadCSV(data, filename) {
            let csv = "Data;Utente;Azione;Dettagli;Modulo;ID;IP;User Agent\n";
            data.forEach(log => {
                const user = log.user_fullName || 'Sistema';
                // Remove newlines and escape quotes for CSV compatibility
                const cleanDetails = (log.details || "").replace(/\r?\n|\r/g, " ").replace(/"/g, '""');
                const ip = log.ip_address === '::1' ? '127.0.0.1' : log.ip_address;
                const date = new Date(log.created_at).toLocaleString('it-IT');
                
                csv += `"${date}";"${user}";"${log.action}";"${cleanDetails}";"${log.model || ''}";"${log.model_id || ''}";"${ip}";"${log.user_agent}"\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.setAttribute("download", filename);
            link.click();
        }

        function printLogs() {
            window.print();
        }

        function printSelected() {
            // For printing selected only, we hide non-selected rows temporarily
            const allRows = document.querySelectorAll('#logs-table tbody tr');
            allRows.forEach(row => {
                const cb = row.querySelector('.row-select');
                if (cb && !cb.checked) row.classList.add('no-print');
            });
            window.print();
            allRows.forEach(row => row.classList.remove('no-print'));
        }
    </script>
</x-app-layout>
