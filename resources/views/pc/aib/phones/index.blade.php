<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">Gestione Telefonia e SIM (AIB)</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPhoneModal">
                <i class="fas fa-plus me-2"></i>Nuova SIM/Telefono
            </button>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
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
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light text-primary me-2 edit-btn" 
                                            data-id="{{ $phone->id }}"
                                            data-numero="{{ $phone->numero }}"
                                            data-alias="{{ $phone->alias }}"
                                            data-operatore="{{ $phone->operatore }}"
                                            data-piano="{{ $phone->piano_telefonico }}"
                                            data-imei="{{ $phone->imei }}"
                                            data-stato="{{ $phone->stato }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editPhoneModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('pc.aib.phones.destroy', $phone->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger" onclick="return confirm('Sei sicuro di voler eliminare questa SIM/Telefono?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
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
</x-app-layout>
