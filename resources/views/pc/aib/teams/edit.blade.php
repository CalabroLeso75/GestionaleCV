<x-app-layout>
    <x-slot name="header">
        Modifica Squadra AIB: {{ $team->sigla }}
    </x-slot>

    @php
       $selStations = $team->stations->pluck('id')->toArray();
       $selVehicles = $team->vehicles->pluck('id')->toArray();
       $selPhones = $team->phones->pluck('id')->toArray();
       $selDevices = $team->mobileDevices->pluck('id')->toArray();
       $selMembers = $team->members; 
       $selMemberIds = $selMembers->pluck('member_id')->toArray();
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Modifica Dettagli e Assegnazioni Squadra</h5>
                    <small class="text-danger">Attenzione: se si modificano i Capisquadra, i cambiamenti verranno annotati nel Registro Consegne Storico per ogni mezzo o dispositivo assegnato.</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('pc.aib.teams.update', $team) }}" method="POST" id="teamForm">
                        @csrf
                        @method('PUT')
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
                                <input type="text" name="sigla" id="siglaInput" class="form-control text-uppercase" value="{{ $team->sigla }}" required oninput="this.value = this.value.toUpperCase()">
                            </div>
                            
                            <div class="col-md-8">
                                <label class="form-label fw-bold" for="stationsSelect">Postazione/i di Base *</label>
                                <select id="stationsSelect" class="form-select resource-select" size="5">
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}" @if(in_array($station->id, $selStations)) hidden disabled style="display:none;" @endif>
                                            {{ $station->nome }} ({{ $station->provincia }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-primary fw-bold"><i class="fas fa-hand-pointer me-1"></i> Fai doppio clic su una postazione per aggiungerla al rendiconto in basso.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Campagna / Periodo</label>
                                <input type="text" name="campagna" class="form-control" value="{{ $team->campagna }}">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Data Inizio *</label>
                                <input type="date" name="data_inizio" class="form-control" value="{{ $team->data_inizio->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Data Fine (Chiusura)</label>
                                <input type="date" name="data_fine" class="form-control" value="{{ $team->data_fine ? $team->data_fine->format('Y-m-d') : '' }}">
                                <small class="text-muted">Imposta per terminare l'operatività.</small>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-truck me-2"></i>Risorse Assegnate</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mezzo/i (AIB) *</label>
                                <select id="vehiclesSelect" class="form-select resource-select" size="5">
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" @if(in_array($vehicle->id, $selVehicles)) hidden disabled style="display:none;" @endif>
                                            {{ $vehicle->targa }} - {{ $vehicle->marca }} {{ $vehicle->modello }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-primary fw-bold"><i class="fas fa-hand-pointer me-1"></i> Doppio clic per selezionare il mezzo.</small>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Telefoni con SIM</label>
                                <select id="phonesSelect" class="form-select resource-select" size="5">
                                    @foreach($company_phones as $phone)
                                        <option value="{{ $phone->id }}" @if(in_array($phone->id, $selPhones)) hidden disabled style="display:none;" @endif>
                                            {{ $phone->numero }} @if($phone->alias)({{ $phone->alias }})@endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Dispositivi (Senza SIM)</label>
                                <select id="mobileDevicesSelect" class="form-select resource-select" size="5">
                                    @foreach($mobile_devices as $device)
                                        <option value="{{ $device->id }}" @if(in_array($device->id, $selDevices)) hidden disabled style="display:none;" @endif>
                                            {{ $device->marca }} {{ $device->modello }} ({{ $device->seriale ?? $device->imei }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Personale AIB (Operatori)</h6>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Seleziona Personale *</label>
                                <select id="employeeSelect" class="form-select resource-select" size="6">
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" data-name="{{ $employee->first_name }} {{ $employee->last_name }}" @if(in_array($employee->id, $selMemberIds)) hidden disabled style="display:none;" @endif>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mt-3">
                                <div id="selectedMembersContainer" class="list-group">
                                    @foreach($selMembers as $i => $tm)
                                    <div class="list-group-item list-group-item-action member-role-row bg-light mb-2 rounded border" data-val="{{ $tm->member_id }}">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 fw-bold">
                                                <i class="fas fa-user text-secondary me-2"></i>{{ $tm->member->first_name }} {{ $tm->member->last_name }}
                                                <input type="hidden" name="members[{{ $i }}][id]" value="{{ $tm->member_id }}">
                                            </div>
                                            <div class="col-md-8 d-flex gap-4 align-items-center">
                                                <div class="form-check form-switch role-check">
                                                    <input class="form-check-input" type="checkbox" name="members[{{ $i }}][is_caposquadra]" value="1" id="capo_{{ $tm->member_id }}" {{ $tm->is_caposquadra ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="capo_{{ $tm->member_id }}">Caposquadra</label>
                                                </div>
                                                <div class="form-check form-switch role-check">
                                                    <input class="form-check-input" type="checkbox" name="members[{{ $i }}][is_autista]" value="1" id="autista_{{ $tm->member_id }}" {{ $tm->is_autista ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="autista_{{ $tm->member_id }}">Autista</label>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="text" name="members[{{ $i }}][ruolo_specifico]" class="form-control form-control-sm" placeholder="Specifica altro ruolo" value="{{ $tm->ruolo_specifico }}">
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-member" title="Rimuovi">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Note Operative</label>
                                <textarea name="note" class="form-control" rows="2">{{ $team->note }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-list-check me-2"></i>Rendiconto Assegnazioni (Risorse)</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div id="rendicontoList" class="list-group">
                                    @foreach($team->stations as $st)
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rendiconto-item shadow-sm mb-1 rounded" data-select="stationsSelect" data-val="{{ $st->id }}" style="cursor: pointer;" title="Clicca per rimuovere">
                                            <div><i class="fas fa-building text-primary me-2"></i> {{ $st->nome }} ({{ $st->provincia }})</div>
                                            <input type="hidden" name="stations[]" value="{{ $st->id }}">
                                            <span class="text-danger"><i class="fas fa-times"></i></span>
                                        </div>
                                    @endforeach
                                    @foreach($team->vehicles as $vh)
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rendiconto-item shadow-sm mb-1 rounded" data-select="vehiclesSelect" data-val="{{ $vh->id }}" style="cursor: pointer;" title="Clicca per rimuovere">
                                            <div><i class="fas fa-truck text-success me-2"></i> {{ $vh->targa }} - {{ $vh->marca }} {{ $vh->modello }}</div>
                                            <input type="hidden" name="vehicles[]" value="{{ $vh->id }}">
                                            <span class="text-danger"><i class="fas fa-times"></i></span>
                                        </div>
                                    @endforeach
                                    @foreach($team->phones as $ph)
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rendiconto-item shadow-sm mb-1 rounded" data-select="phonesSelect" data-val="{{ $ph->id }}" style="cursor: pointer;" title="Clicca per rimuovere">
                                            <div><i class="fas fa-phone text-info me-2"></i> {{ $ph->numero }} @if($ph->alias)({{ $ph->alias }})@endif</div>
                                            <input type="hidden" name="phones[]" value="{{ $ph->id }}">
                                            <span class="text-danger"><i class="fas fa-times"></i></span>
                                        </div>
                                    @endforeach
                                    @foreach($team->mobileDevices as $md)
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rendiconto-item shadow-sm mb-1 rounded" data-select="mobileDevicesSelect" data-val="{{ $md->id }}" style="cursor: pointer;" title="Clicca per rimuovere">
                                            <div><i class="fas fa-mobile-alt text-secondary me-2"></i> {{ $md->marca }} {{ $md->modello }} ({{ $md->seriale ?? $md->imei }})</div>
                                            <input type="hidden" name="mobile_devices[]" value="{{ $md->id }}">
                                            <span class="text-danger"><i class="fas fa-times"></i></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('pc.aib.teams.index') }}" class="btn btn-light me-2">Annulla</a>
                            <button type="submit" class="btn btn-primary btn-lg">Applica Modifiche</button>
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
            let memberIndex = {{ count($selMembers) }};

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
                if (!val) return; 
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
                const text = $option.text().trim();
                let icon = ''; let inputName = '';
                if (selectId === 'stationsSelect') { icon = 'fa-building text-primary'; inputName = 'stations[]'; }
                else if (selectId === 'vehiclesSelect') { icon = 'fa-truck text-success'; inputName = 'vehicles[]'; }
                else if (selectId === 'phonesSelect') { icon = 'fa-phone text-info'; inputName = 'phones[]'; }
                else if (selectId === 'mobileDevicesSelect') { icon = 'fa-mobile-alt text-secondary'; inputName = 'mobile_devices[]'; }

                $option.prop('selected', false).prop('hidden', true).prop('disabled', true).hide();
                $select.val('');
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
                $option.prop('selected', false).prop('hidden', true).prop('disabled', true).hide();
                $('#employeeSelect').val(''); 
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

            $rendicontoList.on('click', '.rendiconto-item', function() {
                if (confirm('Vuoi rimuovere questa risorsa dal rendiconto?')) {
                    const selectId = $(this).data('select');
                    const val = $(this).data('val');
                    $('#' + selectId).find(`option[value="${val}"]`).prop('hidden', false).prop('disabled', false).show();
                    $(this).remove();
                    checkRendicontoEmpty();
                }
            });

            $container.on('click', '.btn-remove-member', function() {
                if (confirm('Vuoi rimuovere questo operatore dalla squadra?')) {
                    const $row = $(this).closest('.member-role-row');
                    const val = $row.data('val');
                    $('#employeeSelect').find(`option[value="${val}"]`).prop('hidden', false).prop('disabled', false).show();
                    $row.remove();
                }
            });

            $('#teamForm').on('submit', function(e) {
                if ($('input[name="stations[]"]').length === 0) { e.preventDefault(); alert('Errore: Devi selezionare almeno una postazione di base!'); return false; }
                if ($('input[name="vehicles[]"]').length === 0) { e.preventDefault(); alert('Errore: Devi assegnare almeno un mezzo AIB alla squadra!'); return false; }
                if ($('.member-role-row').length === 0) { e.preventDefault(); alert('Errore: Devi selezionare almeno un operatore da associare alla squadra!'); return false; }
            });

            checkRendicontoEmpty();
        });
    </script>
    @endpush
</x-app-layout>
