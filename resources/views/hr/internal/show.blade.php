<x-app-layout>
    <x-slot name="header">
        Fascicolo Personale — {{ $employee->last_name }} {{ $employee->first_name }}
    </x-slot>

    <style>
        .section-card { border: none; border-radius: 10px; margin-bottom: 20px; }
        .section-card .card-header { font-weight: 600; font-size: 0.95em; border-radius: 10px 10px 0 0; }
        .form-label { font-size: 0.85em; font-weight: 600; color: #555; margin-bottom: 2px; }
        .status-badge { font-size: 0.85em; }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header con info rapida --}}
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #2e7d32, #43a047); color: white;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ $employee->last_name }} {{ $employee->first_name }}</h4>
                    <div class="d-flex gap-3" style="opacity:0.9; font-size:0.9em;">
                        <span>CF: <strong>{{ $employee->tax_code }}</strong></span>
                        @if($employee->badge_number)
                            <span>Badge: <strong>{{ $employee->badge_number }}</strong></span>
                        @endif
                        <span>ID: <strong>#{{ $employee->id }}</strong></span>
                    </div>
                </div>
                <div class="text-end">
                    @switch($employee->status)
                        @case('active') <span class="badge bg-light text-success status-badge">✓ Attivo</span> @break
                        @case('suspended') <span class="badge bg-light text-warning status-badge">⏸ Sospeso</span> @break
                        @case('terminated') <span class="badge bg-light text-secondary status-badge">Cessato</span> @break
                        @default <span class="badge bg-light text-info status-badge">In attesa</span>
                    @endswitch
                    @if($employee->is_aib_qualified)
                        <br><span class="badge bg-danger mt-1">🔥 Qualificato AIB</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($canEdit)
    <form method="POST" action="{{ route('hr.internal.update', $employee->id) }}"
          onsubmit="return confirm('Salvare le modifiche al fascicolo personale?')">
        @csrf
        @method('PUT')
    @else
    <div>
        <div class="alert alert-info" role="alert">
            👁️ <strong>Modalità visualizzazione</strong> — Non hai i permessi per modificare questi dati.
        </div>
    @endif

        {{-- DATI ANAGRAFICI --}}
        <div class="card section-card shadow-sm">
            <div class="card-header bg-light">📋 Dati Anagrafici</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cognome *</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Codice Fiscale *</label>
                        <input type="text" name="tax_code" class="form-control" value="{{ old('tax_code', $employee->tax_code) }}" {{ $canEdit ? 'required' : 'disabled' }} style="text-transform:uppercase;">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Data di Nascita *</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Luogo di Nascita</label>
                        <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $employee->birth_place) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sesso</label>
                        <select name="gender" class="form-select" {{ !$canEdit ? 'disabled' : '' }}>
                            <option value="">—</option>
                            <option value="male" {{ old('gender', $employee->gender) === 'male' ? 'selected' : '' }}>Maschio</option>
                            <option value="female" {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>Femmina</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Numero Badge</label>
                        <input type="text" name="badge_number" class="form-control" value="{{ old('badge_number', $employee->badge_number) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        {{-- CONTATTI --}}
        <div class="card section-card shadow-sm">
            <div class="card-header bg-light">📞 Contatti</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email Aziendale</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Personale</label>
                        <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email', $employee->personal_email) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefono</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefono Personale</label>
                        <input type="text" name="personal_phone" class="form-control" value="{{ old('personal_phone', $employee->personal_phone) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        {{-- DATI LAVORATIVI --}}
        <div class="card section-card shadow-sm">
            <div class="card-header bg-light">💼 Dati Lavorativi</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Posizione / Mansione</label>
                        <input type="text" name="position" class="form-control" value="{{ old('position', $employee->position) }}" {{ !$canEdit ? 'disabled' : '' }}>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo Dipendente *</label>
                        <select name="employee_type" class="form-select" {{ !$canEdit ? 'disabled' : '' }}>
                            <option value="internal" {{ old('employee_type', $employee->employee_type) === 'internal' ? 'selected' : '' }}>Interno</option>
                            <option value="external" {{ old('employee_type', $employee->employee_type) === 'external' ? 'selected' : '' }}>Esterno</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Stato *</label>
                        <select name="status" class="form-select" {{ !$canEdit ? 'disabled' : '' }}>
                            <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Attivo</option>
                            <option value="suspended" {{ old('status', $employee->status) === 'suspended' ? 'selected' : '' }}>Sospeso</option>
                            <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Cessato</option>
                            <option value="pending" {{ old('status', $employee->status) === 'pending' ? 'selected' : '' }}>In attesa</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-3">
                            <input type="hidden" name="is_aib_qualified" value="0">
                            <input class="form-check-input" type="checkbox" name="is_aib_qualified" value="1" id="aibCheck"
                                   {{ old('is_aib_qualified', $employee->is_aib_qualified) ? 'checked' : '' }} {{ !$canEdit ? 'disabled' : '' }}>
                            <label class="form-check-label" for="aibCheck">🔥 Qualificato AIB</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch mt-3">
                            <input type="hidden" name="is_emergency_available" value="0">
                            <input class="form-check-input" type="checkbox" name="is_emergency_available" value="1" id="emergencyCheck"
                                   {{ old('is_emergency_available', $employee->is_emergency_available) ? 'checked' : '' }} {{ !$canEdit ? 'disabled' : '' }}>
                            <label class="form-check-label" for="emergencyCheck">🚨 Disponibile per Emergenze</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- NOTE --}}
        <div class="card section-card shadow-sm">
            <div class="card-header bg-light">📝 Note</div>
            <div class="card-body">
                <textarea name="notes" class="form-control" rows="3" placeholder="Note aggiuntive..." {{ !$canEdit ? 'disabled' : '' }}>{{ old('notes', $employee->notes) }}</textarea>
            </div>
        </div>

        {{-- DOCUMENTAZIONE (placeholder) --}}
        <div class="card section-card shadow-sm" style="opacity:0.7;">
            <div class="card-header bg-light">📎 Documentazione</div>
            <div class="card-body text-center text-muted py-4">
                <div style="font-size: 2em;">📄</div>
                <p>Sezione documentazione in fase di sviluppo.<br>
                <small>Qui sarà possibile allegare e gestire documenti del fascicolo personale.</small></p>
            </div>
        </div>

    {{-- Close the form/div before the area-roles section (which has its own forms) --}}
    @if($canEdit)
        <div class="d-flex justify-content-end mt-3 mb-2">
            <button type="submit" class="btn btn-success">💾 Salva Modifiche Anagrafica</button>
        </div>
        </form>
    @else
        </div>
    @endif

    {{-- ABILITAZIONI AREE (outside main form) --}}
    <div class="card section-card shadow-sm mt-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span>🔑 Abilitazioni Aree</span>
            @if($linkedUser)
                <small class="text-muted">Utente collegato: <strong>{{ $linkedUser->name }} {{ $linkedUser->surname }}</strong> ({{ $linkedUser->email }})</small>
            @endif
        </div>
        <div class="card-body">
            @if(!$linkedUser)
                <div class="alert alert-warning mb-0">
                    <strong>⚠️ Nessun account utente collegato.</strong><br>
                    Questo dipendente non ha un account utente nel sistema (nessun utente con codice fiscale <code>{{ $employee->tax_code }}</code>).
                    <br>Le abilitazioni aree possono essere gestite solo per utenti con account attivo.
                </div>
            @else
                {{-- Abilitazioni correnti --}}
                @if($areaRoles->count() > 0)
                    <table class="table table-sm table-hover mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Area</th>
                                <th>Ruolo</th>
                                <th style="width: 80px;">Data</th>
                                @if($canEdit)
                                    <th style="width: 50px;"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areaRoles as $ar)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $ar->area }}</span>
                                    </td>
                                    <td>
                                        @switch($ar->role)
                                            @case('responsabile')
                                                <span class="badge bg-danger">Responsabile</span>
                                                @break
                                            @case('operatore')
                                                <span class="badge bg-primary">Operatore</span>
                                                @break
                                            @case('viewer')
                                                <span class="badge bg-secondary">Viewer</span>
                                                @break
                                            @default
                                                <span class="badge bg-info">{{ $ar->role }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-muted" style="font-size:0.85em;">
                                        {{ $ar->created_at ? \Carbon\Carbon::parse($ar->created_at)->format('d/m/Y') : '—' }}
                                    </td>
                                    @if($canEdit)
                                        <td>
                                            <form method="POST" action="{{ route('hr.internal.removeAreaRole', [$employee->id, $ar->id]) }}"
                                                  onsubmit="return confirm('Rimuovere questa abilitazione?')" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Rimuovi">✕</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-3"><em>Nessuna abilitazione area assegnata.</em></p>
                @endif

                {{-- Form aggiungi abilitazione --}}
                @if($canEdit)
                    <div class="border-top pt-3">
                        <h6 class="mb-2" style="font-size:0.9em; font-weight:600;">➕ Aggiungi Abilitazione</h6>
                        <form method="POST" action="{{ route('hr.internal.addAreaRole', $employee->id) }}" class="row g-2 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label small">Area</label>
                                <select name="area" class="form-select form-select-sm" required>
                                    <option value="">— Seleziona area —</option>
                                    @foreach($availableAreas as $area)
                                        <option value="{{ $area }}">{{ $area }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Ruolo</label>
                                <select name="role" class="form-select form-select-sm" required>
                                    <option value="">— Seleziona ruolo —</option>
                                    @foreach($availableRoles as $role)
                                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm btn-success w-100">Aggiungi</button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Azioni finali --}}
    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('hr.internal.index') }}" class="btn btn-outline-secondary">← Torna all'elenco</a>
    </div>

    <div class="mt-2 mb-4 text-muted" style="font-size:0.8em;">
        Ultimo aggiornamento: {{ $employee->updated_at ? $employee->updated_at->format('d/m/Y H:i') : '—' }}
        | Creato: {{ $employee->created_at ? $employee->created_at->format('d/m/Y H:i') : '—' }}
    </div>
</x-app-layout>
