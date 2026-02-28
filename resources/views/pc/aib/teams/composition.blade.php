<x-app-layout>
    <x-slot name="header">
        Nuova Squadra AIB
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Dettagli Operativi Squadra</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pc.aib.teams.store') }}" method="POST" id="teamForm">
                        @csrf
                        
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informazioni Generali</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Sigla Radio *</label>
                                <input type="text" name="sigla" class="form-control" placeholder="es. CZREM001" required>
                                <small class="text-muted">La sigla identificativa della squadra.</small>
                            </div>
                            
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Postazione/i di Base *</label>
                                <select name="stations[]" class="form-select select2-multiple" multiple required data-placeholder="Seleziona le postazioni...">
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->nome }} ({{ $station->provincia }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Campagna</label>
                                <input type="text" name="campagna" class="form-control" placeholder="es. Estiva 2026">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Data Inizio *</label>
                                <input type="date" name="data_inizio" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Data Fine</label>
                                <input type="date" name="data_fine" class="form-control">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Turno *</label>
                                <select name="turno" class="form-select" required>
                                    <option value="Mattina">Mattina</option>
                                    <option value="Pomeriggio">Pomeriggio</option>
                                    <option value="Notte">Notte</option>
                                    <option value="H24">H24</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-truck me-2"></i>Risorse Assegnate</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mezzo/i (AIB)</label>
                                <select name="vehicles[]" class="form-select select2-multiple" multiple data-placeholder="Scegli i mezzi...">
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->targa }} - {{ $vehicle->marca }} {{ $vehicle->modello }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Telefoni di Servizio</label>
                                <select name="phones[]" class="form-select select2-multiple" multiple data-placeholder="Scegli i telefoni...">
                                    @foreach($company_phones as $phone)
                                        <option value="{{ $phone->id }}">{{ $phone->numero }} @if($phone->alias)({{ $phone->alias }})@endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Personale AIB (Operatori)</h6>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Seleziona Personale *</label>
                                <select id="employeeSelect" class="form-select select2-multiple" multiple data-placeholder="Cerca e seleziona il personale...">
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->first_name }} {{ $employee->last_name }}">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">Puoi selezionare tutto il personale che coprirà i vari turni della squadra.</small>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <div id="selectedMembersContainer" class="list-group">
                                    <!-- Dynamic roles settings appear here -->
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Note Operative</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Eventuali annotazioni..."></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('pc.aib.teams.index') }}" class="btn btn-light me-2">Annulla</a>
                            <button type="submit" class="btn btn-primary btn-lg">Salva Squadra</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        .role-check label { cursor: pointer; user-select: none; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for multiple selects
            $('.select2-multiple').select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true
            });

            const $empSelect = $('#employeeSelect');
            const $container = $('#selectedMembersContainer');
            
            // Map to track inputs index
            let memberIndex = 0;

            $empSelect.on('change', function() {
                const selectedOptions = $(this).find('option:selected');
                
                // Get all currently selected IDs
                const currentIds = selectedOptions.map(function() { return $(this).val(); }).get();

                // Remove unselected members from UI
                $container.children('.member-role-row').each(function() {
                    const id = $(this).data('id').toString();
                    if (!currentIds.includes(id)) {
                        $(this).remove();
                    }
                });

                // Add newly selected members to UI
                selectedOptions.each(function() {
                    const id = $(this).val();
                    const name = $(this).data('name');
                    
                    if ($container.find(`.member-role-row[data-id="${id}"]`).length === 0) {
                        const html = `
                            <div class="list-group-item list-group-item-action member-role-row bg-light mb-2 rounded border" data-id="${id}">
                                <div class="row align-items-center">
                                    <div class="col-md-4 fw-bold">
                                        <i class="fas fa-user text-secondary me-2"></i>${name}
                                        <input type="hidden" name="members[${memberIndex}][id]" value="${id}">
                                    </div>
                                    <div class="col-md-8 d-flex gap-4 align-items-center">
                                        <div class="form-check form-switch role-check">
                                            <input class="form-check-input" type="checkbox" name="members[${memberIndex}][is_caposquadra]" value="1" id="capo_${id}">
                                            <label class="form-check-label" for="capo_${id}">Caposquadra</label>
                                        </div>
                                        <div class="form-check form-switch role-check">
                                            <input class="form-check-input" type="checkbox" name="members[${memberIndex}][is_autista]" value="1" id="autista_${id}">
                                            <label class="form-check-label" for="autista_${id}">Autista</label>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" name="members[${memberIndex}][ruolo_specifico]" class="form-control form-control-sm" placeholder="Specifica altro ruolo (opzionale)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $container.append(html);
                        memberIndex++;
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
