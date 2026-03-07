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
                        
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informazioni Generali</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Sigla Radio *</label>
                                <input type="text" name="sigla" id="siglaInput" class="form-control text-uppercase" placeholder="es. CZREM001" required oninput="this.value = this.value.toUpperCase()">
                                <small class="text-muted">La sigla verrà convertita in automatico in maiuscolo.</small>
                            </div>
                            
                            <div class="col-md-8">
                                <label class="form-label fw-bold" for="stationsSelect">Postazione/i di Base *</label>
                                <select id="stationsSelect" class="form-select resource-select" size="5">
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->nome }} ({{ $station->provincia }})</option>
                                    @endforeach
                                </select>
                                <small class="text-primary fw-bold"><i class="fas fa-hand-pointer me-1"></i> Fai doppio clic su una postazione per aggiungerla al rendiconto in basso.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Campagna / Periodo</label>
                                <input type="text" name="campagna" class="form-control" placeholder="es. Estiva 2026">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Data Creazione *</label>
                                <input type="date" name="data_inizio" class="form-control" value="{{ date('Y-m-d') }}" required {{ !auth()->user()->hasRole('super-admin') ? 'readonly' : '' }}>
                                <small class="text-muted">Modificabile solo dagli amministratori di sistema.</small>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-truck me-2"></i>Risorse Assegnate</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mezzo/i (AIB) *</label>
                                <select id="vehiclesSelect" class="form-select resource-select" size="5">
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->targa }} - {{ $vehicle->marca }} {{ $vehicle->modello }}</option>
                                    @endforeach
                                </select>
                                <small class="text-primary fw-bold"><i class="fas fa-hand-pointer me-1"></i> Doppio clic per selezionare il mezzo.</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Telefoni con SIM</label>
                                <select id="phonesSelect" class="form-select resource-select" size="5">
                                    @foreach($company_phones as $phone)
                                        <option value="{{ $phone->id }}">{{ $phone->numero }} @if($phone->alias)({{ $phone->alias }})@endif</option>
                                    @endforeach
                                </select>
                                <small class="text-primary fw-bold"><i class="fas fa-hand-pointer me-1"></i> Doppio clic per inserire.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Dispositivi (Senza SIM)</label>
                                <select id="mobileDevicesSelect" class="form-select resource-select" size="5">
                                    @foreach($mobile_devices as $device)
                                        <option value="{{ $device->id }}">{{ $device->marca }} {{ $device->modello }} ({{ $device->seriale ?? $device->imei }})</option>
                                    @endforeach
                                </select>
                                <small class="text-primary fw-bold"><i class="fas fa-hand-pointer me-1"></i> Doppio clic per inserire.</small>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Personale AIB (Operatori)</h6>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Seleziona Personale *</label>
                                <select id="employeeSelect" class="form-select resource-select" size="6">
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->first_name }} {{ $employee->last_name }}">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-primary fw-bold"><i class="fas fa-hand-pointer me-1"></i> Fai doppio clic su un operatore per aggiungerlo all'elenco dei turni (Rendiconto in basso).</small>
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

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-list-check me-2"></i>Rendiconto Assegnazioni (Risorse)</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div id="rendicontoList" class="list-group">
                                    <div class="text-muted p-3 text-center border rounded bg-light">Nessuna risorsa (Postazioni, Mezzi, Telefoni) selezionata.</div>
                                </div>
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
    <style>
        .role-check label { cursor: pointer; user-select: none; }
    </style>
    @endpush

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const $empSelect = $('#employeeSelect');
            const $container = $('#selectedMembersContainer');
            const $rendicontoList = $('#rendicontoList');
            let memberIndex = 0;

            function checkRendicontoEmpty() {
                if ($rendicontoList.children('.rendiconto-item').length === 0) {
                    if ($('#rendiconto-empty').length === 0) {
                        $rendicontoList.html('<div id="rendiconto-empty" class="text-muted p-3 text-center border rounded bg-light">Nessuna risorsa (Postazioni, Mezzi, Telefoni) selezionata.</div>');
                    }
                } else {
                    $('#rendiconto-empty').remove();
                }
            }

            $('.resource-select').on('dblclick', function(e) {
                e.preventDefault();
                const $select = $(this);
                const val = $select.val();
                
                if (!val) return; // Cliccato su spazio vuoto

                const $option = $select.find(`option[value="${val}"]`);
                if (!$option.length || $option.prop('hidden') || $option.prop('disabled')) return;

                const selectId = $select.attr('id');

                if (selectId === 'employeeSelect') {
                    addEmployeeToRendiconto($option);
                } else {
                    addResourceToRendiconto($select, $option, selectId);
                }
            });

            function addResourceToRendiconto($select, $option, selectId) {
                const val = $option.val();
                const text = $option.text();
                
                let icon = '';
                let inputName = '';
                if (selectId === 'stationsSelect') { icon = 'fa-building text-primary'; inputName = 'stations[]'; }
                else if (selectId === 'vehiclesSelect') { icon = 'fa-truck text-success'; inputName = 'vehicles[]'; }
                else if (selectId === 'phonesSelect') { icon = 'fa-phone text-info'; inputName = 'phones[]'; }
                else if (selectId === 'mobileDevicesSelect') { icon = 'fa-mobile-alt text-secondary'; inputName = 'mobile_devices[]'; }

                // Hide option to "remove" it from available
                $option.prop('selected', false).prop('hidden', true).prop('disabled', true).hide();
                $select.val(''); // Deseleziona tutto

                // Add to rendiconto
                const html = `
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rendiconto-item shadow-sm mb-1 rounded" data-select="${selectId}" data-val="${val}" style="cursor: pointer;" title="Clicca per rimuovere">
                        <div><i class="fas ${icon} me-2"></i> ${text}</div>
                        <input type="hidden" name="${inputName}" value="${val}">
                        <span class="text-danger"><i class="fas fa-times"></i></span>
                    </div>
                `;
                $rendicontoList.append(html);
                checkRendicontoEmpty();
            }

            function addEmployeeToRendiconto($option) {
                const val = $option.val();
                const name = $option.data('name');

                // Hide option
                $option.prop('selected', false).prop('hidden', true).prop('disabled', true).hide();
                $('#employeeSelect').val(''); // Deseleziona

                const html = `
                    <div class="list-group-item list-group-item-action member-role-row bg-light mb-2 rounded border" data-val="${val}">
                        <div class="row align-items-center">
                            <div class="col-md-4 fw-bold">
                                <i class="fas fa-user text-secondary me-2"></i>${name}
                                <input type="hidden" name="members[${memberIndex}][id]" value="${val}">
                            </div>
                            <div class="col-md-8 d-flex gap-4 align-items-center">
                                <div class="form-check form-switch role-check">
                                    <input class="form-check-input" type="checkbox" name="members[${memberIndex}][is_caposquadra]" value="1" id="capo_${val}">
                                    <label class="form-check-label" for="capo_${val}">Caposquadra</label>
                                </div>
                                <div class="form-check form-switch role-check">
                                    <input class="form-check-input" type="checkbox" name="members[${memberIndex}][is_autista]" value="1" id="autista_${val}">
                                    <label class="form-check-label" for="autista_${val}">Autista</label>
                                </div>
                                <div class="flex-grow-1">
                                    <input type="text" name="members[${memberIndex}][ruolo_specifico]" class="form-control form-control-sm" placeholder="Specifica altro ruolo (opzionale)">
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-member" title="Rimuovi">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $container.append(html);
                memberIndex++;
            }

            // Remove from rendiconto
            $rendicontoList.on('click', '.rendiconto-item', function() {
                if (confirm('Vuoi rimuovere questa risorsa dal rendiconto?')) {
                    const selectId = $(this).data('select');
                    const val = $(this).data('val');
                    $('#' + selectId).find(`option[value="${val}"]`).prop('hidden', false).prop('disabled', false).show();
                    $(this).remove();
                    checkRendicontoEmpty();
                }
            });

            // Remove employee
            $container.on('click', '.btn-remove-member', function() {
                if (confirm('Vuoi rimuovere questo operatore dalla squadra?')) {
                    const $row = $(this).closest('.member-role-row');
                    const val = $row.data('val');
                    $('#employeeSelect').find(`option[value="${val}"]`).prop('hidden', false).prop('disabled', false).show();
                    $row.remove();
                }
            });

            // Form validation
            $('#teamForm').on('submit', function(e) {
                if ($('input[name="stations[]"]').length === 0) {
                    e.preventDefault();
                    alert('Errore: Devi selezionare almeno una postazione di base!');
                    return false;
                }
                if ($('input[name="vehicles[]"]').length === 0) {
                    e.preventDefault();
                    alert('Errore: Devi assegnare almeno un mezzo AIB alla squadra!');
                    return false;
                }
                if ($('.member-role-row').length === 0) {
                    e.preventDefault();
                    alert('Errore: Devi selezionare almeno un operatore da associare alla squadra!');
                    return false;
                }
            });

            // Initial check
            checkRendicontoEmpty();
        });
    </script>
    @endpush
</x-app-layout>
