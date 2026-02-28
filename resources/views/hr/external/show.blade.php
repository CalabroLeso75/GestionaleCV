<x-app-layout>
    <x-slot name="header">
        Dettaglio Collaboratore Esterno — {{ $employee->last_name }} {{ $employee->first_name }}
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #1565c0, #1e88e5); color: white;">
        <div class="card-body p-4">
            <h4 class="mb-1">{{ $employee->last_name }} {{ $employee->first_name }}</h4>
            <div style="opacity:0.9; font-size:0.9em;">
                CF: <strong>{{ $employee->tax_code }}</strong>
                | ID: <strong>#{{ $employee->id }}</strong>
                @if($employee->job_title)
                    | Mansione: <strong>{{ $employee->job_title }}</strong>
                @endif
            </div>
        </div>
    </div>

    @if($canEdit)
    <form method="POST" action="{{ route('hr.external.update', $employee->id) }}"
          onsubmit="return confirm('Salvare le modifiche?')">
        @csrf
        @method('PUT')
    @else
    <div>
        <div class="alert alert-info" role="alert">
            👁️ <strong>Modalità visualizzazione</strong> — Non hai i permessi per modificare questi dati.
        </div>
    @endif

        <div class="card shadow-sm border-0 mb-3" style="border-radius:10px;">
            <div class="card-header bg-light fw-bold" style="font-size:0.95em;">📋 Dati Anagrafici</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Cognome *</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" {{ $canEdit ? 'required' : 'disabled' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Nome *</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" {{ $canEdit ? 'required' : 'disabled' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Codice Fiscale *</label>
                        <input type="text" name="tax_code" class="form-control" value="{{ old('tax_code', $employee->tax_code) }}" {{ $canEdit ? 'required' : 'disabled' }} style="text-transform:uppercase;">
                    </div>
                     <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Data di Nascita *</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}" {{ $canEdit ? 'required' : 'disabled' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Mansione</label>
                        <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $employee->job_title) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Luogo di Nascita</label>
                        <input type="text" name="birth_place_text" class="form-control" value="{{ old('birth_place_text', $employee->birth_place_text) }}" {{ !$canEdit ? 'disabled' : '' }}>
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
                        <select name="organization_id" id="organization_id" class="form-select" {{ $canEdit ? 'required' : 'disabled' }}>
                            <option value="">— Seleziona organizzazione —</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" data-is-aib="{{ $org->is_aib }}" {{ old('organization_id', $employee->organization_id) == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }} {{ $org->is_aib ? '(AIB)' : '' }}
                                </option>
                            @endforeach
                            <option value="new" {{ old('organization_id') == 'new' ? 'selected' : '' }}>➕ Altre organizzazioni...</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_aib" id="is_aib" value="1" {{ old('is_aib', $employee->is_aib) ? 'checked' : '' }} {{ !$canEdit ? 'disabled' : '' }}>
                            <label class="form-check-label fw-bold" for="is_aib">🔥 Qualificato AIB</label>
                            <div class="small text-muted" id="aib_notice" style="display:none;">
                                ℹ️ Selezione automatica basata sull'organizzazione.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modulo nuova organizzazione --}}
                <div id="new_organization_form" class="mt-4 p-3 border rounded bg-light" style="{{ old('organization_id') == 'new' ? '' : 'display:none;' }}">
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
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $employee->start_date ? $employee->start_date->format('Y-m-d') : '') }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Data Fine</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $employee->end_date ? $employee->end_date->format('Y-m-d') : '') }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        {{-- Spazio placeholder --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius:10px; opacity:0.7;">
            <div class="card-header bg-light fw-bold" style="font-size:0.95em;">📎 Documentazione e Altre Attività</div>
            <div class="card-body text-center text-muted py-4">
                <div style="font-size: 2em;">🔧</div>
                <p class="mb-0">Spazio riservato per future integrazioni:<br>
                <small>Documentazione, contratti, presenze, valutazioni.</small></p>
            </div>
        </div>

        @if($canEdit)
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('hr.external.index') }}" class="btn btn-outline-secondary">← Torna all'elenco</a>
            <button type="submit" class="btn btn-success">💾 Salva Modifiche</button>
        </div>
    </form>
    @else
        <div class="mt-3">
            <a href="{{ route('hr.external.index') }}" class="btn btn-outline-secondary">← Torna all'elenco</a>
        </div>
    </div>
    @endif

    <div class="mt-2 mb-4 text-muted" style="font-size:0.8em;">
        Ultimo aggiornamento: {{ $employee->updated_at ?? '—' }}
    </div>

    <script>
        const orgSelect = document.getElementById('organization_id');
        if (orgSelect) {
            orgSelect.addEventListener('change', function() {
                const val = this.value;
                const selected = this.options[this.selectedIndex];
                const isAib = selected ? selected.getAttribute('data-is-aib') === '1' : false;
                
                const aibSwitch = document.getElementById('is_aib');
                const notice = document.getElementById('aib_notice');
                const newOrgForm = document.getElementById('new_organization_form');
                const newOrgName = document.getElementById('new_org_name');

                // Handle "New Organization" form visibility
                if (val === 'new') {
                    if (newOrgForm) newOrgForm.style.display = 'block';
                    if (newOrgName) newOrgName.setAttribute('required', 'required');
                } else {
                    if (newOrgForm) newOrgForm.style.display = 'none';
                    if (newOrgName) newOrgName.removeAttribute('required');
                }

                // Handle AIB auto-toggle
                if (isAib && val !== 'new') {
                    if (aibSwitch) aibSwitch.checked = true;
                    if (notice) notice.style.display = 'block';
                } else {
                    if (notice) notice.style.display = 'none';
                }
            });

            // Trigger on page load
            window.addEventListener('DOMContentLoaded', () => {
                orgSelect.dispatchEvent(new Event('change'));
            });
        }
    </script>
</x-app-layout>
