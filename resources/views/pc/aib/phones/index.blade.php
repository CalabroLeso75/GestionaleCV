<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark mb-0">Gestione SIM Telefoniche</h2>
    </x-slot>

    <div class="row g-4 mb-4">
        {{-- Statistiche Rapide - stile Risorse Umane --}}
        <x-kpi-card color="green"  label="Totale SIM"        :value="$phones->count()" />
        <x-kpi-card color="blue"   label="Attive"             :value="$phones->where('stato', 'Attivo')->count()" />
        <x-kpi-card color="orange" label="Assegnate"          :value="$phones->filter(function($p) { return $p->activeAssignment !== null; })->count()" />
        <x-kpi-card color="red"    label="Inattive / Scadute" :value="$phones->where('stato', 'Inattivo')->count()" />
        
        @can('vehicle.full_edit')
        <div class="col-md-3">
            <div class="card shadow-sm border-0 d-flex flex-row align-items-stretch text-white h-100 overflow-hidden">
                <div class="w-50 bg-primary d-flex align-items-center justify-content-center border-end border-light" 
                     style="cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1"
                     data-bs-toggle="modal" data-bs-target="#createPhoneModal" title="Inserimento SIM Singola">
                    <div class="text-center p-2">
                        <div class="fs-3 mb-1"><i class="fas fa-sim-card"></i></div>
                        <small class="fw-bold" style="font-size:0.75em;">SINGOLO</small>
                    </div>
                </div>
                <div class="w-50 bg-primary d-flex align-items-center justify-content-center" 
                     style="cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1"
                     data-bs-toggle="modal" data-bs-target="#bulkPhoneModal" title="Inserimento Multiplo">
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
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Numero</th>
                                <th>Alias / Assegnazione</th>
                                <th>Operatore</th>
                                <th>Piano Telefonico</th>
                                <th>IMEI</th>
                                <th>Stato</th>
                                <th>Assegnazione</th>
                                <th class="text-end pe-4">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($phones as $phone)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $phone->numero }}</td>
                                <td>{{ $phone->alias ?? '-' }}</td>
                                <td>{{ $phone->operatore ?? '-' }}</td>
                                <td>{{ $phone->piano_telefonico ?? '-' }}</td>
                                <td class="text-muted"><small>{{ $phone->imei ?? '-' }}</small></td>
                                <td>
                                    @if($phone->stato === 'Attivo')
                                        <span class="badge bg-success-subtle text-success">Attivo</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inattivo</span>
                                    @endif
                                </td>
                                <td>
                                    @if($phone->activeAssignment)
                                        <div class="small">
                                            <span class="fw-bold text-primary">
                                                @if($phone->activeAssignment->assignee)
                                                    {{ $phone->activeAssignment->assignee->first_name }} {{ $phone->activeAssignment->assignee->last_name }}
                                                @else
                                                    N/D
                                                @endif
                                            </span>
                                            <br><span class="text-muted" style="font-size: 0.8em;">Dal {{ $phone->activeAssignment->data_assegnazione->format('d/m H:i') }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Nessuno</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 text-nowrap">
                                    @if($phone->stato == 'Attivo')
                                        @if(!$phone->activeAssignment)
                                            <button class="btn btn-sm btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#assignModal{{ $phone->id }}" title="Assegna">
                                                <i class="fas fa-user-plus"></i> <span class="d-none d-md-inline ms-1">Assegna</span>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#returnModal{{ $phone->id }}" title="Riconsegna">
                                                <i class="fas fa-undo"></i> <span class="d-none d-md-inline ms-1">Riconsegna</span>
                                            </button>
                                        @endif
                                    @endif

                                    <button class="btn btn-sm btn-outline-primary me-2 edit-btn" 
                                            data-id="{{ $phone->id }}"
                                            data-numero="{{ $phone->numero }}"
                                            data-alias="{{ $phone->alias }}"
                                            data-operatore="{{ $phone->operatore }}"
                                            data-piano="{{ $phone->piano_telefonico }}"
                                            data-imei="{{ $phone->imei }}"
                                            data-stato="{{ $phone->stato }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editPhoneModal" title="Modifica">
                                        <i class="fas fa-edit"></i> <span class="d-none d-md-inline ms-1">Modifica</span>
                                    </button>
                                    <form action="{{ route('pc.aib.phones.destroy', $phone->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Sei sicuro di voler eliminare questa SIM/Telefono?')" title="Elimina">
                                            <i class="fas fa-trash"></i> <span class="d-none d-md-inline ms-1">Elimina</span>
                                        </button>
                                    </form>

                                    <!-- Modals -->
                                    @if(!$phone->activeAssignment)
                                    <!-- Assign Modal -->
                                    <div class="modal fade text-start" id="assignModal{{ $phone->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('pc.aib.resource_assignments.store') }}">
                                                    @csrf
                                                    <input type="hidden" name="assignable_type" value="App\Models\CompanyPhone">
                                                    <input type="hidden" name="assignable_id" value="{{ $phone->id }}">
                                                    <input type="hidden" name="assignee_type" value="App\Models\InternalEmployee">
                                                    
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Assegna SIM/Telefono: {{ $phone->numero }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Dipendente Interno</label>
                                                            <select name="assignee_id" class="form-select" required>
                                                                @foreach(\App\Models\InternalEmployee::orderBy('last_name')->get() as $emp)
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
                                    <div class="modal fade text-start" id="returnModal{{ $phone->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="{{ route('pc.aib.resource_assignments.return', $phone->activeAssignment->id) }}">
                                                    @csrf
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title">Riconsegna SIM/Telefono: {{ $phone->numero }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-center p-4">
                                                        <div class="mb-3 text-start">
                                                            <label class="form-label small">Note di riconsegna (Es. Danni, Smagnetizzata, etc.)</label>
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
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-sim-card fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">Nessuna SIM o telefono inserito nel sistema.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createPhoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('pc.aib.phones.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title">Nuova SIM / Telefono</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Numero *</label>
                            <input type="text" name="numero" class="form-control" required placeholder="Es. 3331234567">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alias o Assegnazione provvisoria</label>
                            <input type="text" name="alias" class="form-control" placeholder="Es. Cellulare DOS / Radio base">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Operatore</label>
                                <input type="text" name="operatore" class="form-control" placeholder="Es. TIM">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Stato *</label>
                                <select name="stato" class="form-select" required>
                                    <option value="Attivo">Attivo</option>
                                    <option value="Inattivo">Inattivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Piano Telefonico</label>
                            <input type="text" name="piano_telefonico" class="form-control" placeholder="Es. Dati illimitati + 500 min">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">IMEI Apparato (opzionale)</label>
                            <input type="text" name="imei" class="form-control" placeholder="Es. 3522190... (15 cifre)">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Salva Dati</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Bulk Modal -->
    <div class="modal fade" id="bulkPhoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('pc.aib.phones.bulkStore') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i>Inserimento SIM in Blocco</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 px-3 small border-0 mt-2">
                            <i class="fas fa-info-circle me-2"></i>Verifica che il numero iniziale termini con delle cifre. Verranno generati automaticamente N numeri consecutivi incrementando il valore di +1. (es: Inserendo 3331234567 e Quantità 3, genererà 3331234567, 3331234568 e 3331234569).
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-8">
                                <label class="form-label fw-bold">Numero Iniziale *</label>
                                <input type="text" name="numero_iniziale" class="form-control" required placeholder="Es. 3331234500">
                            </div>
                            <div class="col-4">
                                <label class="form-label fw-bold">Quantità *</label>
                                <input type="number" name="quantita" class="form-control" required min="1" max="100" value="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alias Assegnato al Blocco</label>
                            <input type="text" name="alias" class="form-control" placeholder="Es. SIM Dati Moduli AIB">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Operatore</label>
                                <input type="text" name="operatore" class="form-control" placeholder="Es. TIM">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Stato *</label>
                                <select name="stato" class="form-select" required>
                                    <option value="Attivo">Attivo</option>
                                    <option value="Inattivo">Inattivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Piano Telefonico Comune</label>
                            <input type="text" name="piano_telefonico" class="form-control" placeholder="Es. Dati illimitati + 500 min">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-magic me-2"></i>Genera SIM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editPhoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom-0">
                        <h5 class="modal-title">Modifica SIM / Telefono</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Numero *</label>
                            <input type="text" name="numero" id="edit_numero" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alias / Assegnazione</label>
                            <input type="text" name="alias" id="edit_alias" class="form-control">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Operatore</label>
                                <input type="text" name="operatore" id="edit_operatore" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Stato *</label>
                                <select name="stato" id="edit_stato" class="form-select" required>
                                    <option value="Attivo">Attivo</option>
                                    <option value="Inattivo">Inattivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Piano Telefonico</label>
                            <input type="text" name="piano_telefonico" id="edit_piano" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">IMEI Apparato</label>
                            <input type="text" name="imei" id="edit_imei" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Aggiorna Dati</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    document.getElementById('editForm').action = `/GestionaleCV/pc/aib/telefoni/${id}`;
                    document.getElementById('edit_numero').value = this.dataset.numero || '';
                    document.getElementById('edit_alias').value = this.dataset.alias || '';
                    document.getElementById('edit_operatore').value = this.dataset.operatore || '';
                    document.getElementById('edit_piano').value = this.dataset.piano || '';
                    document.getElementById('edit_imei').value = this.dataset.imei || '';
                    document.getElementById('edit_stato').value = this.dataset.stato || 'Attivo';
                });
            });
        });
    </script>
    @endpush

    <x-back-button :url="route('magazzino.index')" label="← Torna al Magazzino" />

</x-app-layout>
