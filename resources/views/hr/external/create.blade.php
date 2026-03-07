@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp
<x-app-layout>
    <x-slot name="header">
        Nuovo Collaboratore Esterno
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('hr.external.index') }}" class="btn btn-outline-secondary">← Torna all'elenco</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hr.external.store') }}" onsubmit="return confirm('Registrare il nuovo collaboratore?')">
        @csrf

        <div class="card shadow-sm border-0 mb-3" style="border-radius:10px;">
            <div class="card-header bg-light fw-bold" style="font-size:0.95em;">📋 Dati Anagrafici</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Cognome *</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Nome *</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Codice Fiscale *</label>
                        <input type="text" name="tax_code" class="form-control" value="{{ old('tax_code') }}" required style="text-transform:uppercase;" maxlength="16">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Data di Nascita *</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Mansione</label>
                        <input type="text" name="job_title" class="form-control" value="{{ old('job_title') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Luogo di Nascita</label>
                        <input type="text" name="birth_place_text" class="form-control" value="{{ old('birth_place_text') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3" style="border-radius:10px;">
            <div class="card-header bg-light fw-bold" style="font-size:0.95em;">🏢 Organizzazione e Qualifica</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Organizzazione di Provenienza *</label>
                        <select name="organization_id" id="organization_id" class="form-select" required>
                            <option value="">— Seleziona organizzazione —</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" data-is-aib="{{ $org->is_aib }}" {{ old('organization_id') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }} {{ $org->is_aib ? '(AIB)' : '' }}
                                </option>
                            @endforeach
                            <option value="new" {{ old('organization_id') == 'new' ? 'selected' : '' }}>➕ Altre organizzazioni...</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end" id="aib_toggle_wrapper">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_aib" id="is_aib" value="1" {{ old('is_aib') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="is_aib">🔥 Qualificato AIB</label>
                            <div class="small text-muted" id="aib_notice" style="display:none;">
                                ℹ️ Selezione automatica basata sull'organizzazione.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modulo nuova organizzazione --}}
                <div id="new_organization_form" class="mt-4 p-3 border rounded bg-light" @if(old('organization_id') != 'new') style="display:none;" @endif>
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle me-1"></i> Nuova Organizzazione</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nome Organizzazione *</label>
                            <input type="text" name="new_org_name" id="new_org_name" class="form-control form-control-sm" value="{{ old('new_org_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tipo *</label>
                            <select name="new_org_type" id="new_org_type" class="form-select form-select-sm">
                                <option value="private" {{ old('new_org_type') == 'private' ? 'selected' : '' }}>Privato</option>
                                <option value="public" {{ old('new_org_type') == 'public' ? 'selected' : '' }}>Pubblico</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Codice Fiscale / P.IVA</label>
                            <input type="text" name="new_org_tax_code" class="form-control form-control-sm" value="{{ old('new_org_tax_code') }}">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="new_org_is_aib" id="new_org_is_aib" value="1" {{ old('new_org_is_aib') ? 'checked' : '' }}>
                                <label class="form-check-label small fw-bold" for="new_org_is_aib">
                                    Questa organizzazione è abilitata AIB (Antincendio Boschivo)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3" style="border-radius:10px;">
            <div class="card-header bg-light fw-bold" style="font-size:0.95em;">📅 Periodo Collaborazione</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Data Inizio</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Data Fine</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-success px-4">🚀 Registra Collaboratore</button>
        </div>
    </form>

    <script>
        document.getElementById('organization_id').addEventListener('change', function() {
            const val = this.value;
            const selected = this.options[this.selectedIndex];
            const isAib = selected ? selected.getAttribute('data-is-aib') === '1' : false;
            
            const aibSwitch = document.getElementById('is_aib');
            const notice = document.getElementById('aib_notice');
            const newOrgForm = document.getElementById('new_organization_form');
            const newOrgName = document.getElementById('new_org_name');

            // Handle "New Organization" form visibility
            if (val === 'new') {
                newOrgForm.style.display = 'block';
                if (newOrgName) newOrgName.setAttribute('required', 'required');
            } else {
                newOrgForm.style.display = 'none';
                if (newOrgName) newOrgName.removeAttribute('required');
            }

            // Handle AIB auto-toggle
            if (isAib && val !== 'new') {
                aibSwitch.checked = true;
                notice.style.display = 'block';
            } else {
                notice.style.display = 'none';
            }
        });

        // Also trigger on page load if "new" was already selected (e.g. after validation error)
        window.addEventListener('DOMContentLoaded', () => {
            document.getElementById('organization_id').dispatchEvent(new Event('change'));
        });
    </script>
</x-app-layout>
