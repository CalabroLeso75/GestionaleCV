<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark mb-0">Dispositivi Mobili – Smartphone & Tablet</h2>
    </x-slot>

    <div class="row g-4 mb-4">
        {{-- Statistiche Rapide - stile Risorse Umane --}}
        <x-kpi-card color="green"  label="Totale Dispositivi"  :value="$devices->count()" />
        <x-kpi-card color="blue"   label="Attivi"               :value="$devices->where('stato', 'Attivo')->count()" />
        <x-kpi-card color="orange" label="In Uso"               :value="$devices->filter(function($p) { return $p->activeAssignment !== null; })->count()" />
        <x-kpi-card color="purple" label="Inattivi / Dismessi"  :value="$devices->whereIn('stato', ['Inattivo', 'Manutenzione', 'Dismesso'])->count()" />
        
        @can('vehicle.full_edit')
        <div class="col-md-3">
            <div class="card shadow-sm border-0 d-flex flex-row align-items-stretch text-white h-100 overflow-hidden">
                <a href="{{ route('pc.aib.mobile_devices.create') }}" class="w-50 bg-primary d-flex align-items-center justify-content-center border-end border-light text-decoration-none text-white" 
                     style="cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1"
                     title="Inserimento Singolo">
                    <div class="text-center p-2">
                        <div class="fs-3 mb-1"><i class="fas fa-mobile-alt"></i></div>
                        <small class="fw-bold" style="font-size:0.75em;">SINGOLO</small>
                    </div>
                </a>
                <div class="w-50 bg-primary d-flex align-items-center justify-content-center" 
                     style="cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1"
                     data-bs-toggle="modal" data-bs-target="#bulkDeviceModal" title="Inserimento Multiplo">
                    <div class="text-center p-2">
                        <div class="fs-3 mb-1"><i class="fas fa-layer-group"></i></div>
                        <small class="fw-bold" style="font-size:0.75em;">BLOCCO</small>
                    </div>
                </div>
            </div>
        </div>
        @endcan
        
        <!-- Lista Tabelle -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0 overflow-hidden">
                <div class="card-body p-0 table-responsive">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th>Tipo</th>
                                    <th>Marca / Modello</th>
                                    <th>Cod. Inv.</th>
                                    <th>IMEI</th>
                                    <th>OS</th>
                                    <th>Stato</th>
                                    <th>Assegnazione</th>
                                    <th class="text-center">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($devices as $device)
                                    <tr>
                                        <td>
                                            @if(($device->tipo ?? 'smartphone') == 'tablet')
                                                <span class="badge bg-info text-dark">💻 Tablet</span>
                                            @else
                                                <span class="badge bg-light text-dark border">📱 Smartphone</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $device->marca }}</strong><br>
                                            <small class="text-muted">{{ $device->modello }}</small>
                                        </td>
                                        <td><code>{{ $device->asset_code ?: '-' }}</code></td>
                                        <td><small>{{ $device->imei ?: '-' }}</small></td>
                                        <td>
                                            @if($device->sistema_operativo)
                                                <small>{{ $device->sistema_operativo }} {{ $device->versione_os }}</small>
                                            @else
                                                <small class="text-muted">-</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($device->stato == 'Attivo')
                                                <span class="badge bg-success">Attivo</span>
                                            @elseif($device->stato == 'Inattivo')
                                                <span class="badge bg-secondary">Inattivo</span>
                                            @elseif($device->stato == 'Manutenzione')
                                                <span class="badge bg-warning text-dark">Manutenzione</span>
                                            @else
                                                <span class="badge bg-danger">Dismesso</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($device->activeAssignment)
                                                <div class="small">
                                                    <span class="fw-bold text-primary">
                                                        @if($device->activeAssignment->assignee)
                                                            {{ $device->activeAssignment->assignee->last_name }} {{ $device->activeAssignment->assignee->first_name }}
                                                        @else
                                                            N/D
                                                        @endif
                                                    </span>
                                                    <br><span class="text-muted" style="font-size: 0.8em;">Dal {{ $device->activeAssignment->data_assegnazione->format('d/m H:i') }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted small">Nessuno</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap">
                                            @if($device->stato == 'Attivo')
                                                @if(!$device->activeAssignment)
                                                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#assignModal{{ $device->id }}" title="Assegna">
                                                        <i class="fas fa-user-plus"></i> <span class="d-none d-md-inline ms-1">Assegna</span>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#returnModal{{ $device->id }}" title="Riconsegna">
                                                        <i class="fas fa-undo"></i> <span class="d-none d-md-inline ms-1">Riconsegna</span>
                                                    </button>
                                                @endif
                                            @endif
                                            
                                            <a href="{{ route('pc.aib.mobile_devices.edit', $device) }}" class="btn btn-sm btn-outline-primary ms-1" title="Modifica">
                                                <i class="fas fa-edit"></i> <span class="d-none d-md-inline ms-1">Modifica</span>
                                            </a>
                                            <form action="{{ route('pc.aib.mobile_devices.destroy', $device) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Sei sicuro di voler eliminare questo dispositivo?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">
                                                    <i class="fas fa-trash"></i> <span class="d-none d-md-inline ms-1">Elimina</span>
                                                </button>
                                            </form>
                                            
                                            <!-- Resource Assignment Modals embedded -->
                                            @if(!$device->activeAssignment)
                                            <!-- Assign Modal -->
                                            <div class="modal fade" id="assignModal{{ $device->id }}" tabindex="-1">
                                                <div class="modal-dialog text-start">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('pc.aib.resource_assignments.store') }}">
                                                            @csrf
                                                            <input type="hidden" name="assignable_type" value="App\Models\MobileDevice">
                                                            <input type="hidden" name="assignable_id" value="{{ $device->id }}">
                                                            <input type="hidden" name="assignee_type" value="App\Models\InternalEmployee">
                                                            
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Assegna Dispositivo: {{ $device->modello }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Dipendente Interno</label>
                                                                    <select name="assignee_id" class="form-select" required>
                                                                        @foreach($employees as $emp)
                                                                            <option value="{{ $emp->id }}">{{ $emp->last_name }} {{ $emp->first_name }}</option>
                                                                        @endforeach
                                                                    </select>
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
                                            @else
                                            <!-- Return Modal -->
                                            <div class="modal fade" id="returnModal{{ $device->id }}" tabindex="-1">
                                                <div class="modal-dialog text-start">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('pc.aib.resource_assignments.return', $device->activeAssignment->id) }}">
                                                            @csrf
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title">Riconsegna Dispositivo: {{ $device->modello }}</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body text-center p-4">
                                                                <div class="mb-3 text-start">
                                                                    <label class="form-label small">Note di riconsegna (Es. Danni al display)</label>
                                                                    <textarea name="note" class="form-control" rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-success w-100 fw-bold">Registra Riconsegna</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="fas fa-mobile-alt fa-3x mb-3 text-light"></i><br>
                                            <p class="mb-0">Nessun dispositivo mobile registrato.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Bulk Modal -->
    <div class="modal fade" id="bulkDeviceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('pc.aib.mobile_devices.bulkStore') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i>Inserimento Telefoni in Blocco</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 px-3 small border-0 mt-2">
                            <i class="fas fa-info-circle me-2"></i>Genera dispositivi con la stessa base di IMEI incrementata o, se IMEI vuoto, crea N copie dello stesso modello pronte per futuri aggiornamenti IMEI.
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-8">
                                <label class="form-label fw-bold">IMEI Base (opzionale)</label>
                                <input type="text" name="imei_iniziale" class="form-control" placeholder="Es. 350000000000001">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold">Quantità *</label>
                                <input type="number" name="quantita" class="form-control" required min="1" max="100" value="1">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Marca *</label>
                                <input type="text" name="marca" class="form-control" required placeholder="Es. Samsung">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Modello *</label>
                                <input type="text" name="modello" class="form-control" required placeholder="Es. Galaxy XCover">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stato *</label>
                            <select name="stato" class="form-select" required>
                                <option value="Attivo">Attivo</option>
                                <option value="Inattivo">Inattivo</option>
                                <option value="Manutenzione">Manutenzione</option>
                                <option value="Dismesso">Dismesso</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note Comuni</label>
                            <input type="text" name="note" class="form-control" placeholder="Es. Fornitura 2026">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-magic me-2"></i>Genera Telefoni</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-back-button :url="route('magazzino.index')" label="← Torna al Magazzino" />

</x-app-layout>
