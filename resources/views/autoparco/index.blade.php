<x-app-layout>
    <x-slot name="header">
        Autoparco - Gestione Flotta
    </x-slot>

    <div class="row g-4">
        <!-- Banner Scadenze -->
        @if($expiringAssicurazione->count() > 0 || $expiringRevisione->count() > 0)
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-warning bg-opacity-10 border-start border-warning border-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3 fs-3 text-warning">⚠️</div>
                        <div>
                            <h5 class="mb-1 fw-bold">Attenzione: Scadenze Imminenti</h5>
                            <p class="mb-0 small text-muted">
                                @if($expiringAssicurazione->count() > 0)
                                    Assicurazione in scadenza per: <b>{{ $expiringAssicurazione->pluck('targa')->implode(', ') }}</b>.
                                @endif
                                @if($expiringRevisione->count() > 0)
                                    Revisione in scadenza per: <b>{{ $expiringRevisione->pluck('targa')->implode(', ') }}</b>.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Statistiche Rapide -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100">
                <div class="text-primary fs-2 mb-2">🚗</div>
                <h3 class="mb-0 fw-bold">{{ $vehicles->count() }}</h3>
                <small class="text-muted text-uppercase">Totale Mezzi</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100 border-start border-success border-4">
                <div class="text-success fs-2 mb-2">✅</div>
                <h3 class="mb-0 fw-bold">{{ $vehicles->where('stato', 'operativo')->count() }}</h3>
                <small class="text-muted text-uppercase">Operativi</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100 border-start border-info border-4">
                <div class="text-info fs-2 mb-2">🔑</div>
                <h3 class="mb-0 fw-bold">{{ $vehicles->where('stato', 'in uso')->count() }}</h3>
                <small class="text-muted text-uppercase">In Uso</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100 border-start border-danger border-4">
                <div class="text-danger fs-3 mb-2">⚠️</div>
                <h3 class="mb-0 fw-bold">{{ $vehicles->whereIn('stato', ['manutenzione', 'non operativo'])->count() }}</h3>
                <small class="text-muted text-uppercase">Fermo Macchine</small>
            </div>
        </div>
        @can('vehicle.full_edit')
        <div class="col-md-3">
            <div class="card shadow-sm border-0 d-flex align-items-center justify-content-center h-100 bg-primary text-white" 
                 style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                <div class="text-center">
                    <div class="fs-2 mb-1">+</div>
                    <small class="fw-bold">NUOVO MEZZO</small>
                </div>
            </div>
        </div>
        @endcan

        <!-- Lista Mezzi -->
        <div class="col-12">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-header bg-white py-0 border-0">
                    <ul class="nav nav-tabs card-header-tabs" id="autoparcoTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold text-primary py-3 border-0" id="vehicles-tab" data-bs-toggle="tab" data-bs-target="#tab-vehicles" type="button" role="tab">
                                <i class="fas fa-truck me-2"></i>Parco Macchine
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold text-primary py-3 border-0" id="personnel-tab" data-bs-toggle="tab" data-bs-target="#tab-personnel" type="button" role="tab">
                                <i class="fas fa-id-card me-2"></i>Abilitazioni Personale
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-vehicles" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Mezzo</th>
                                <th>Targa</th>
                                <th>KM Attuali</th>
                                <th>Stato</th>
                                <th>Safety Compliance</th>
                                <th>Utilizzatore Attuale</th>
                                <th class="text-end">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 rounded bg-light p-2" style="font-size: 1.2em;">
                                            @if($vehicle->tipo == 'Pickup') 🛻 @elseif($vehicle->tipo == 'Autobotte') 🚛 @else 🚗 @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $vehicle->marca }} {{ $vehicle->modello }}</div>
                                            <small class="text-muted">{{ $vehicle->tipo }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-dark font-monospace px-3">{{ $vehicle->targa }}</span></td>
                                <td>{{ number_format($vehicle->km_attuali, 0, ',', '.') }} km</td>
                                <td>
                                    @php
                                        $colors = [
                                            'operativo' => 'success', 
                                            'in uso' => 'info', 
                                            'manutenzione' => 'warning', 
                                            'non operativo' => 'danger',
                                            'alienazione' => 'dark'
                                        ];
                                        $labels = [
                                            'operativo' => 'Operativo',
                                            'in uso' => 'In Uso',
                                            'manutenzione' => 'In Manutenzione',
                                            'non operativo' => 'Non Operativo',
                                            'alienazione' => 'Fuori Uso / Alienazione'
                                        ];
                                        $c = $colors[$vehicle->stato] ?? 'secondary';
                                        $l = $labels[$vehicle->stato] ?? ucfirst($vehicle->stato);
                                    @endphp
                                    <span class="badge rounded-pill bg-{{ $c }}">{{ $l }}</span>
                                </td>
                                <td>
                                    @php
                                        $rev_days = $vehicle->scadenza_revisione ? now()->diffInDays($vehicle->scadenza_revisione, false) : -999;
                                        $ass_days = $vehicle->scadenza_assicurazione ? now()->diffInDays($vehicle->scadenza_assicurazione, false) : -999;
                                        
                                        $is_expired = ($rev_days < 0 || $ass_days < 0);
                                        $is_warning = ($rev_days < 30 || $ass_days < 30);
                                    @endphp
                                    @if($is_expired)
                                        <span class="badge bg-danger">NON COMPLIANT</span>
                                    @elseif($is_warning)
                                        <span class="badge bg-warning text-dark">ATTENZIONE</span>
                                    @else
                                        <span class="badge bg-success">REGOLARE</span>
                                    @endif
                                </td>
                                <td>
                                    @if($vehicle->currentLog)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8em;">
                                                {{ substr($vehicle->currentLog->user->name, 0, 1) }}{{ substr($vehicle->currentLog->user->surname, 0, 1) }}
                                            </div>
                                            <div class="small">
                                                <div class="fw-bold">{{ $vehicle->currentLog->user->name }} {{ $vehicle->currentLog->user->surname }}</div>
                                                <div class="text-muted" style="font-size: 0.8em;">Dal {{ $vehicle->currentLog->assegnato_il->format('d/m H:i') }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">Nessuno</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary" onclick="showVehicleDetails({{ $vehicle->id }})" title="Dettagli e Revisioni">
                                            🔍
                                        </button>
                                        @if($vehicle->stato == 'operativo')
                                            @can('vehicle.assign')
                                            <button class="btn btn-outline-primary px-3" data-bs-toggle="modal" data-bs-target="#assignModal{{ $vehicle->id }}">
                                                Assegna
                                            </button>
                                            @endcan
                                        @elseif($vehicle->stato == 'in uso')
                                            @can('vehicle.assign')
                                            <button class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#returnModal{{ $vehicle->id }}">
                                                Riconsegna
                                            </button>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Modals embedded in loop (simplification for now, better outside but needs mapping) -->
                            <!-- Assign Modal -->
                            <div class="modal fade" id="assignModal{{ $vehicle->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('autoparco.assign', $vehicle->id) }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Assegna Mezzo: {{ $vehicle->targa }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Utente</label>
                                                    <select name="user_id" class="form-select" required>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->surname }} {{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">KM alla consegna</label>
                                                    <input type="number" name="km_iniziali" class="form-control" value="{{ $vehicle->km_attuali }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Note</label>
                                                    <textarea name="note" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary w-100">Conferma Assegnazione</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Return Modal -->
                            <div class="modal fade" id="returnModal{{ $vehicle->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('autoparco.return', $vehicle->id) }}">
                                            @csrf
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Riconsegna Mezzo: {{ $vehicle->targa }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center p-4">
                                                <div class="fs-1 mb-3">🏁</div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">KM alla Riconsegna</label>
                                                    <input type="number" name="km_finali" class="form-control form-control-lg text-center" 
                                                           value="{{ $vehicle->km_attuali }}" required min="{{ $vehicle->km_attuali }}">
                                                    <div class="form-text">KM iniziali: {{ $vehicle->currentLog->km_iniziali ?? 'N/D' }}</div>
                                                </div>
                                                <div class="mb-3 text-start">
                                                    <label class="form-label small">Note di riconsegna (Danni, pulizia, etc.)</label>
                                                    <textarea name="note" class="form-control" rows="2"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success w-100">Concludi Utilizzo</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted italic">Nessun mezzo registrato nel parco macchine.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        
        <div class="tab-pane fade" id="tab-personnel" role="tabpanel">
            <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 fw-bold text-muted">Registro Certificazioni & Patenti</h6>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCertificationModal">
                        + AGGIUNGI ABILITAZIONE
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle small">
                        <thead class="bg-light">
                            <tr>
                                <th>Nome Cognome</th>
                                <th>Abilitazioni Attive</th>
                                <th>Documenti</th>
                                <th>Stato Compliance</th>
                                <th class="text-end">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $user->name }} {{ $user->surname }}</div>
                                        <div class="text-muted" style="font-size: 0.8em;">{{ $user->email }}</div>
                                    </td>
                                    <td>
                                        @php $certs = \App\Models\OperatorCertification::where('user_id', $user->id)->get(); @endphp
                                        @forelse($certs as $cert)
                                            <span class="badge bg-info text-dark me-1" title="Scadenza: {{ $cert->scadenza instanceof \Carbon\Carbon ? $cert->scadenza->format('d/m/Y') : (is_string($cert->scadenza) ? $cert->scadenza : 'N/D') }}">
                                                {{ $cert->tipo }}
                                            </span>
                                        @empty
                                            <span class="text-muted italic">Nessuna abilitazione</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="small">
                                            @foreach($certs as $cert)
                                                @if($cert->documento)
                                                    <div><i class="fas fa-file-alt me-1"></i>{{ $cert->tipo }}: {{ $cert->documento }}</div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @php 
                                            $expired = $certs->where('scadenza', '<', now())->count();
                                            $expiring = $certs->whereBetween('scadenza', [now(), now()->addDays(30)])->count();
                                        @endphp
                                        @if($expired > 0)
                                            <span class="badge bg-danger">SCADUTO</span>
                                        @elseif($expiring > 0)
                                            <span class="badge bg-warning text-dark">IN SCADENZA</span>
                                        @elseif($certs->count() > 0)
                                            <span class="badge bg-success">REGOLARE</span>
                                        @else
                                            <span class="badge bg-secondary">INCOMPLETO</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-xs btn-outline-primary" onclick="manageCerts({{ $user->id }}, '{{ $user->name }} {{ $user->surname }}')">GESTISCI</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">Nessun utente trovato</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>

    <!-- Modal Dettagli Mezzo -->
    <div class="modal fade" id="vehicleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="detailsModalTitle">Dettagli Mezzo</h5>
                    <div class="ms-auto me-3">
                        <ul class="nav nav-pills nav-sm" id="vehicleTab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active py-1 text-white border-0" data-bs-toggle="tab" data-bs-target="#tab-info">Scheda Tecnica</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link py-1 text-white border-0" data-bs-toggle="tab" data-bs-target="#tab-logs" onclick="loadVehicleLogs()">Registro Attività</button>
                            </li>
                        </ul>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="tab-content">
                        <!-- Tab info -->
                        <div class="tab-pane fade show active" id="tab-info">
                            <div class="row g-4">
                                <!-- Colonna Sinistra: Specifiche -->
                                <div class="col-lg-4">
                                    <div class="card shadow-sm h-100 border-0">
                                        <div class="card-header bg-white d-flex justify-content-between">
                                            <h6 class="mb-0 fw-bold">Anagrafica & Status</h6>
                                            @can('vehicle.full_edit')
                                                <button class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.7em;" onclick="editVehicle(currentVehicleId)">MODIFICA</button>
                                            @endcan
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-borderless small">
                                                <tr><th class="text-muted">Marca/Modello:</th><td id="det_marca_modello">-</td></tr>
                                                <tr><th class="text-muted">Tipo:</th><td id="det_tipo">-</td></tr>
                                                <tr><th class="text-muted">Classificazione:</th><td id="det_normativa">-</td></tr>
                                                <tr><th class="text-muted">Immatricolazione:</th><td id="det_imm">-</td></tr>
                                                <tr><th class="text-muted">KM Attuali:</th><td id="det_km">-</td></tr>
                                                <tr><th class="text-muted">Stato:</th><td id="det_stato">-</td></tr>
                                            </table>
                                            
                                            @can('vehicle.limited_edit')
                                            <form id="statusForm" method="POST" class="mt-3">
                                                @csrf
                                                <label class="form-label small fw-bold">Aggiorna Stato Operativo</label>
                                                <div class="input-group input-group-sm">
                                                    <select name="stato" class="form-select" id="det_stato_select">
                                                        <option value="operativo">Operativo</option>
                                                        <option value="manutenzione">In Manutenzione</option>
                                                        <option value="non operativo">Non Operativo</option>
                                                        <option value="alienazione">Fine vita / Alienazione</option>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary px-3">Aggiorna</button>
                                                </div>
                                            </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>

                                <!-- Colonna Centrale: Assicurazione -->
                                <div class="col-lg-4">
                                    <div class="card shadow-sm h-100 border-0">
                                        <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Dati Assicurativi</h6></div>
                                        <div class="card-body">
                                            <div class="alert alert-info py-2 px-3 small border-0">
                                                <div class="row">
                                                    <div class="col-6"><strong>Compagnia:</strong></div>
                                                    <div class="col-6" id="det_ass_comp">-</div>
                                                    <div class="col-6"><strong>Polizza:</strong></div>
                                                    <div class="col-6" id="det_ass_pol">-</div>
                                                </div>
                                            </div>
                                            <table class="table table-sm table-borderless small mt-2">
                                                <tr><th class="text-muted">Scadenza:</th><td id="det_ass_scad" class="fw-bold">-</td></tr>
                                                <tr><th class="text-muted">Fine Copertura (+15gg):</th><td id="det_ass_cop" class="text-danger">-</td></tr>
                                            </table>
                                            @can('vehicle.limited_edit')
                                                <button class="btn btn-xs btn-outline-info w-100 small mt-2" style="font-size: 0.8em;">Aggiorna Polizza</button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>

                                <!-- Colonna Destra: Revisione -->
                                <div class="col-lg-4">
                                    <div class="card shadow-sm h-100 border-0">
                                        <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Stato Revisione</h6></div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <div class="small text-muted">Prossima Revisione:</div>
                                                <div class="h4 fw-bold text-primary mb-0" id="det_rev_scad">-</div>
                                                <div id="det_rev_rule" class="small italic text-muted mt-1">Regola: 4/2 anni</div>
                                            </div>
                                            
                                            @can('vehicle.limited_edit')
                                            <button class="btn btn-sm btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#newRevisionForm">
                                                + Aggiungi Esito Revisione
                                            </button>
                                            
                                            <div class="collapse mt-2" id="newRevisionForm">
                                                <form id="revisionForm" method="POST" class="p-2 border rounded bg-white">
                                                    @csrf
                                                    <div class="row g-2">
                                                        <div class="col-6"><input type="date" name="data_revisione" class="form-control form-control-sm" required></div>
                                                        <div class="col-6"><input type="number" name="km_rilevati" class="form-control form-control-sm" placeholder="KM rilevati" required></div>
                                                        <div class="col-12">
                                                            <select name="esito" class="form-select form-select-sm" required>
                                                                <option value="regolare">Regolare</option>
                                                                <option value="da ripetere">Da Ripetere</option>
                                                                <option value="sospeso">Sospeso</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12"><button type="submit" class="btn btn-sm btn-primary w-100">Salva Revisione</button></div>
                                                    </div>
                                                </form>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>

                                <!-- Storico Revisioni Completo (Full Width) -->
                                <div class="col-12">
                                    <div class="card shadow-sm border-0">
                                        <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Storico Revisioni Effettuate</h6></div>
                                        <div class="table-responsive">
                                            <table class="table table-sm mb-0 small">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Data Revisione</th>
                                                        <th>Esito</th>
                                                        <th>KM al momento</th>
                                                        <th>Note</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="det_revisions_list">
                                                    <!-- populated by js -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab logs -->
                        <div class="tab-pane fade" id="tab-logs">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white"><h6 class="mb-0 fw-bold">Registro Attività per il Mezzo</h6></div>
                                <div class="table-responsive" style="max-height: 400px;">
                                    <table class="table table-sm table-hover align-middle mb-0 small">
                                        <thead class="bg-light sticky-top">
                                            <tr>
                                                <th>Data</th>
                                                <th>Utente</th>
                                                <th>Azione</th>
                                                <th>Dettagli</th>
                                            </tr>
                                        </thead>
                                        <tbody id="det_activity_logs">
                                            <tr><td colspan="4" class="text-center py-4">Caricamento in corso...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.BOOTSTRAP_ITALIA_SPRITES = "{{ asset('public/sprites.php') }}";
        window.vehicleTypes = @json($vehicleTypes);
        let currentVehicleId = null;

        function showNormativeDetails(vtId, type = 'add') {
            const container = document.getElementById(`normative_details_${type}`);
            if (!container) return;
            
            if (!vtId) {
                container.classList.add('d-none');
                return;
            }

            const vt = window.vehicleTypes.find(t => t.id == vtId);
            if (vt) {
                container.classList.remove('d-none');
                container.querySelector('.normative-text').innerHTML = `
                    <strong>Doc:</strong> ${vt.documentazione}<br>
                    <strong>Cert:</strong> ${vt.certificazioni}<br>
                    <strong>Patente:</strong> ${vt.patente}<br>
                    <strong>Review:</strong> ${vt.revisione} | <strong>Ass:</strong> ${vt.assicurazione}
                `;
            } else {
                container.classList.add('d-none');
            }
        }

        function showVehicleDetails(id) {
            currentVehicleId = id;
            const modalEl = document.getElementById('vehicleDetailsModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
            // Reset to first tab
            const infoTab = document.querySelector('#vehicleTab button[data-bs-target="#tab-info"]');
            if (infoTab) infoTab.click();
            document.getElementById('det_activity_logs').innerHTML = '<tr><td colspan="4" class="text-center py-4">Caricamento in corso...</td></tr>';

            fetch(`{{ url('/autoparco') }}/${id}`)
                .then(r => r.json())
                .then(v => {
                    const fmtDateDisplay = (d) => {
                        if (!d) return '-';
                        const date = new Date(d);
                        return isNaN(date.getTime()) ? d : date.toLocaleDateString('it-IT');
                    };

                    document.getElementById('detailsModalTitle').innerText = `Dettagli Mezzo: ${v.targa}`;
                    document.getElementById('det_marca_modello').innerText = `${v.marca} ${v.modello}`;
                    document.getElementById('det_tipo').innerText = v.tipo;
                    document.getElementById('det_normativa').innerText = v.vehicle_type ? v.vehicle_type.name : '-';
                    document.getElementById('det_imm').innerText = `${v.immatricolazione_mese || '--'}/${v.immatricolazione_anno || '----'}`;
                    
                    document.getElementById('det_km').innerText = `${v.km_attuali.toLocaleString()} km`;
                    document.getElementById('det_stato').innerText = v.stato.toUpperCase();
                    
                    const statoSelect = document.getElementById('det_stato_select');
                    if (statoSelect) statoSelect.value = v.stato;
                    
                    document.getElementById('det_ass_comp').innerText = v.assicurazione_compagnia || '-';
                    document.getElementById('det_ass_pol').innerText = v.assicurazione_polizza || '-';
                    document.getElementById('det_ass_scad').innerText = v.scadenza_assicurazione ? new Date(v.scadenza_assicurazione).toLocaleDateString() : '-';
                    
                    if (v.assicurazione_copertura) {
                        document.getElementById('det_ass_cop').innerText = new Date(v.assicurazione_copertura).toLocaleDateString();
                    } else if (v.scadenza_assicurazione) {
                        const cop = new Date(v.scadenza_assicurazione);
                        cop.setDate(cop.getDate() + 15);
                        document.getElementById('det_ass_cop').innerText = cop.toLocaleDateString() + ' (Auto)';
                    } else {
                        document.getElementById('det_ass_cop').innerText = '-';
                    }
                    
                    document.getElementById('det_rev_scad').innerText = v.scadenza_revisione ? new Date(v.scadenza_revisione).toLocaleDateString() : 'DA DEFINIRE';
                    
                    // Forms
                    const statusForm = document.getElementById('statusForm');
                    if (statusForm) statusForm.action = `{{ url('/autoparco') }}/${id}/status`;
                    
                    const revisionForm = document.getElementById('revisionForm');
                    if (revisionForm) revisionForm.action = `{{ url('/autoparco') }}/${id}/revision`;
                    
                    // Revisions list
                    const tbody = document.getElementById('det_revisions_list');
                    tbody.innerHTML = '';
                    if (v.revisions && v.revisions.length > 0) {
                        v.revisions.forEach(rev => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${new Date(rev.data_revisione).toLocaleDateString()}</td>
                                    <td><span class="badge ${rev.esito === 'regolare' ? 'bg-success' : 'bg-warning'}">${rev.esito.toUpperCase()}</span></td>
                                    <td>${rev.km_rilevati ? rev.km_rilevati.toLocaleString() : '-'} km</td>
                                    <td>${rev.note || '-'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">Nessuna revisione registrata negli archivi.</td></tr>';
                    }
                    
                    modal.show();
                });
        }

        function loadVehicleLogs() {
            if (!currentVehicleId) return;
            
            const tbody = document.getElementById('det_activity_logs');
            
            fetch(`{{ url('/autoparco') }}/${currentVehicleId}/logs`)
                .then(r => r.json())
                .then(logs => {
                    tbody.innerHTML = '';
                    if (logs.length > 0) {
                        logs.forEach(log => {
                            tbody.innerHTML += `
                                <tr>
                                    <td class="text-nowrap">${new Date(log.created_at).toLocaleString()}</td>
                                    <td><div class="small fw-bold">${log.user ? (log.user.name + ' ' + log.user.surname) : 'System'}</div></td>
                                    <td><span class="badge bg-secondary">${log.action.toUpperCase()}</span></td>
                                    <td>${log.details || '-'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Nessuna attività registrata per questo mezzo.</td></tr>';
                    }
                });
        }

        function editVehicle(id) {
            const detModalEl = document.getElementById('vehicleDetailsModal');
            let detModal = bootstrap.Modal.getInstance(detModalEl);
            if (detModal) detModal.hide();

            fetch(`{{ url('/autoparco') }}/${id}`)
                .then(r => r.json())
                .then(v => {
                    document.getElementById('editVehicleForm').action = `{{ url('/autoparco') }}/${id}`;
                    document.getElementById('edit_targa').value = v.targa;
                    document.getElementById('edit_marca').value = v.marca;
                    document.getElementById('edit_modello').value = v.modello;
                    document.getElementById('edit_tipo').value = v.tipo;
                    document.getElementById('edit_vehicle_type_id').value = v.vehicle_type_id || '';
                    showNormativeDetails(v.vehicle_type_id, 'edit');
                    
                    const fmtDateForInput = (d) => {
                        if (!d) return '';
                        // Handles "YYYY-MM-DD HH:MM:SS" or "YYYY-MM-DDTHH:MM:SS"
                        return d.split('T')[0].split(' ')[0];
                    };
                    
                    document.getElementById('edit_imm_mese').value = v.immatricolazione_mese || '';
                    document.getElementById('edit_imm_anno').value = v.immatricolazione_anno || '';
                    document.getElementById('edit_ass_comp').value = v.assicurazione_compagnia || '';
                    document.getElementById('edit_ass_pol').value = v.assicurazione_polizza || '';
                    document.getElementById('edit_ass_scad').value = fmtDateForInput(v.scadenza_assicurazione);
                    document.getElementById('edit_ass_cop').value = fmtDateForInput(v.assicurazione_copertura);
                    document.getElementById('edit_km').value = v.km_attuali;
                    document.getElementById('edit_rev_last').value = fmtDateForInput(v.ultima_revisione);

                    const editModalEl = document.getElementById('editVehicleModal');
                    if (editModalEl) {
                        const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                        editModal.show();
                    }
                });
        }

        function manageCerts(userId, userName) {
            document.getElementById('cert_user_id').value = userId;
            document.getElementById('cert_user_name').innerText = userName;
            fetch(`{{ url('/autoparco/certifications') }}/${userId}`)
                .then(r => r.json())
                .then(certs => {
                    const list = document.getElementById('cert_list');
                    list.innerHTML = '';
                    certs.forEach(c => {
                        list.innerHTML += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${c.tipo}</strong><br>
                                    <small class="text-muted">Scadenza: ${new Date(c.scadenza).toLocaleDateString()}</small><br>
                                    <small>${c.documento || ''}</small>
                                </div>
                                <button class="btn btn-xs btn-outline-danger" onclick="deleteCert(${c.id})">Rimuovi</button>
                            </li>
                        `;
                    });
                    const modalEl = document.getElementById('manageCertificationsModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    }
                });
        }

        function deleteCert(id) {
            if (!confirm('Rimuovere questa abilitazione?')) return;
            fetch(`{{ url('/autoparco/certifications') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                location.reload(); // Simple refresh to update both tabs
            });
        }

        document.getElementById('addCertificationForm')?.addEventListener('submit', handleCertSubmit);
        document.getElementById('globalAddCertificationForm')?.addEventListener('submit', handleCertSubmit);

        function handleCertSubmit(e) {
            e.preventDefault();
            const fd = new FormData(this);
            fetch(`{{ url('/autoparco/certifications') }}`, {
                method: 'POST',
                body: fd,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            }).then(r => r.json()).then(res => {
                if (res.success) location.reload();
                else alert(res.error || 'Errore durante il salvataggio');
            });
        }

        // Auto-calculate coverage date (+15 days)
        document.addEventListener('change', function(e) {
            if (e.target.name === 'scadenza_assicurazione') {
                const val = e.target.value;
                if (val) {
                    const scadenza = new Date(val);
                    scadenza.setDate(scadenza.getDate() + 15);
                    const modal = e.target.closest('.modal');
                    const coverageInput = modal.querySelector('input[name="assicurazione_copertura"]');
                    if (coverageInput) {
                        coverageInput.value = scadenza.toISOString().split('T')[0];
                    }
                }
            }
        });
    </script>
    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addVehicleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="{{ route('autoparco.store') }}">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Registra Nuovo Mezzo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Targa *</label>
                                <input type="text" name="targa" class="form-control" placeholder="es. AB123CD" required style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Marca *</label>
                                <input type="text" name="marca" class="form-control" placeholder="es. Fiat, Iveco" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Modello *</label>
                                <input type="text" name="modello" class="form-control" placeholder="es. Panda, Daily" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo Mezzo *</label>
                                <select name="tipo" class="form-select" required>
                                    <option value="Auto">Autovettura Standard</option>
                                    <option value="Pickup">Pickup con modulo AIB</option>
                                    <option value="Autobotte">Autobotte</option>
                                    <option value="Mezzo 9 posti">Trasporto Personale (9 posti)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Normativa *</label>
                                <select name="vehicle_type_id" class="form-select" required onchange="showNormativeDetails(this.value, 'add')">
                                    <option value="">Seleziona...</option>
                                    @foreach($vehicleTypes as $vt)
                                        <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                             <div class="col-md-3">
                                <label class="form-label">Mese Imm. (1-12)</label>
                                <input type="number" name="immatricolazione_mese" class="form-control" min="1" max="12">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Anno Imm.</label>
                                <input type="number" name="immatricolazione_anno" class="form-control" min="1900" max="2099">
                            </div>
                            
                            <hr class="my-2">
                            <h6 class="mb-0 text-primary">Dati Assicurativi</h6>
                            <div class="col-md-6">
                                <label class="form-label">Compagnia</label>
                                <input type="text" name="assicurazione_compagnia" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">N. Polizza</label>
                                <input type="text" name="assicurazione_polizza" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Scadenza Polizza</label>
                                <input type="date" name="scadenza_assicurazione" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fine Copertura (+15gg)</label>
                                <input type="date" name="assicurazione_copertura" class="form-control">
                            </div>

                            <hr class="my-2">
                            <div class="col-md-6">
                                <label class="form-label">KM Attuali *</label>
                                <input type="number" name="km_attuali" class="form-control" value="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data Ultima Revisione</label>
                                <input type="date" name="ultima_revisione" class="form-control">
                                <div class="small text-muted" style="font-size: 0.75rem;">Se vuota, calcola 4 anni dall'immatricolazione.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary px-4">Salva Mezzo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Vehicle Modal -->
    <div class="modal fade" id="editVehicleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editVehicleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">Modifica Dati Mezzo</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Targa *</label>
                                <input type="text" name="targa" id="edit_targa" class="form-control" required style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Marca *</label>
                                <input type="text" name="marca" id="edit_marca" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Modello *</label>
                                <input type="text" name="modello" id="edit_modello" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tipo Mezzo *</label>
                                <select name="tipo" id="edit_tipo" class="form-select" required>
                                    <option value="Auto">Autovettura Standard</option>
                                    <option value="Pickup">Pickup con modulo AIB</option>
                                    <option value="Autobotte">Autobotte</option>
                                    <option value="Mezzo 9 posti">Trasporto Personale (9 posti)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Normativa *</label>
                                <select name="vehicle_type_id" id="edit_vehicle_type_id" class="form-select" required onchange="showNormativeDetails(this.value, 'edit')">
                                    <option value="">Seleziona...</option>
                                    @foreach($vehicleTypes as $vt)
                                        <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mese Imm. (1-12)</label>
                                <input type="number" name="immatricolazione_mese" id="edit_imm_mese" class="form-control" min="1" max="12">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Anno Imm.</label>
                                <input type="number" name="immatricolazione_anno" id="edit_imm_anno" class="form-control" min="1900" max="2099">
                            </div>
                            
                            <hr class="my-2">
                            <h6 class="mb-0 text-primary">Dati Assicurativi</h6>
                            <div class="col-md-6">
                                <label class="form-label">Compagnia</label>
                                <input type="text" name="assicurazione_compagnia" id="edit_ass_comp" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">N. Polizza</label>
                                <input type="text" name="assicurazione_polizza" id="edit_ass_pol" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Scadenza Polizza</label>
                                <input type="date" name="scadenza_assicurazione" id="edit_ass_scad" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fine Copertura (+15gg)</label>
                                <input type="date" name="assicurazione_copertura" id="edit_ass_cop" class="form-control">
                            </div>

                            <hr class="my-2">
                            <div class="col-md-6">
                                <label class="form-label">KM Attuali *</label>
                                <input type="number" name="km_attuali" id="edit_km" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data Ultima Revisione</label>
                                <input type="date" name="ultima_revisione" id="edit_rev_last" class="form-control">
                                <div class="small text-muted" style="font-size: 0.75rem;">La scadenza verrà ricalcolata automaticamente.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-success px-4">Aggiorna Dati</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Manage Certifications Modal -->
    <div class="modal fade" id="manageCertificationsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-dark">
                    <h5 class="modal-title">Gestione Abilitazioni: <span id="cert_user_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Abilitazioni Attuali</h6>
                    <ul class="list-group mb-4" id="cert_list">
                        <!-- Populated by JS -->
                    </ul>

                    <hr>
                    <h6>Aggiungi Nuova Abilitazione</h6>
                    <form id="addCertificationForm" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="cert_user_id">
                        <div class="mb-3">
                            <label class="form-label">Tipo Abilitazione</label>
                            <select name="tipo" class="form-select" required>
                                <option value="Patente B">Patente B</option>
                                <option value="Patente C">Patente C</option>
                                <option value="CQC">CQC</option>
                                <option value="ADR">ADR</option>
                                <option value="Patentino Gru">Patentino Gru</option>
                                <option value="Patentino Mezzi Movimento Terra">Patentino Mezzi Movimento Terra</option>
                                <option value="Abilitazione Carrello Elevatore">Abilitazione Carrello Elevatore</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data Scadenza</label>
                            <input type="date" name="scadenza" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Documento / Note</label>
                            <input type="text" name="documento" class="form-control" placeholder="es. n. patente o rif. documento">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">AGGIUNGI</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Certification Modal (Global) -->
    <div class="modal fade" id="addCertificationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Aggiungi Abilitazione</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="globalAddCertificationForm" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Operatore *</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Seleziona operatore...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->surname }} {{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo Abilitazione</label>
                            <select name="tipo" class="form-select" required>
                                <option value="Patente B">Patente B</option>
                                <option value="Patente C">Patente C</option>
                                <option value="CQC">CQC</option>
                                <option value="ADR">ADR</option>
                                <option value="Patentino Gru">Patentino Gru</option>
                                <option value="Patentino Mezzi Movimento Terra">Patentino Mezzi Movimento Terra</option>
                                <option value="Abilitazione Carrello Elevatore">Abilitazione Carrello Elevatore</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data Scadenza</label>
                            <input type="date" name="scadenza" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Documento / Note</label>
                            <input type="text" name="documento" class="form-control" placeholder="es. n. patente o rif. documento">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">AGGIUNGI</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
