<x-app-layout>
    <x-slot name="header">
        Rubrica Email (SOUP / COP / TEST)
    </x-slot>

    <div class="row g-4 mb-4 align-items-center">
        <div class="col-12 col-md-8">
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-address-book text-primary"></i> Gestione Destinatari Email</h1>
            <p class="text-muted small mb-0 mt-1">Configura gli indirizzi email a cui verranno inviati i report DOS e le segnalazioni.</p>
        </div>
        <div class="col-12 col-md-4 text-md-right">
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addEmailModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> Nuovo Destinatario
            </button>
            <a href="{{ route('admin.index') }}" class="btn btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-left"></i> Torna Indietro
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Elenco Destinatari Attivi e Inattivi</h6>
            <span class="badge badge-primary">{{ count($recipients) }} Indirizzi</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Nome/Ente</th>
                            <th>Email</th>
                            <th>Ruolo</th>
                            <th>Territorio (Prov/Comune)</th>
                            <th>Stato</th>
                            <th class="text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recipients as $email)
                            <tr>
                                <td class="font-weight-bold">{{ $email->name }}</td>
                                <td><a href="mailto:{{ $email->email_address }}">{{ $email->email_address }}</a></td>
                                <td>
                                    @if($email->role_type == 'soup')
                                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-warehouse"></i> SOUP</span>
                                    @elseif($email->role_type == 'cop')
                                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-building"></i> COP</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1"><i class="fas fa-vial"></i> TEST</span>
                                    @endif
                                </td>
                                <td>
                                    @if($email->province)
                                        <span class="badge badge-info">{{ $email->province }}</span>
                                    @endif
                                    @if($email->municipality)
                                        <small class="text-muted ml-1">{{ $email->municipality }}</small>
                                    @endif
                                    @if(!$email->province && !$email->municipality)
                                        <span class="text-muted small">Regionale / Tutte</span>
                                    @endif
                                </td>
                                <td>
                                    @if($email->is_active)
                                        <span class="text-success small"><i class="fas fa-circle"></i> Attivo</span>
                                    @else
                                        <span class="text-danger small"><i class="fas fa-circle"></i> Disabilitato</span>
                                    @endif
                                </td>
                                <td class="text-right text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editEmailModal{{ $email->id }}" title="Modifica">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.emails.destroy', $email->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sei sicuro di voler eliminare questa email?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Elimina">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editEmailModal{{ $email->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content border-0 shadow">
                                        <form action="{{ route('admin.emails.update', $email->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title font-weight-bold text-gray-800">Modifica Destinatario</h5>
                                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <input type="text" class="form-control" name="name" id="edit_name_{{ $email->id }}" value="{{ $email->name }}" required>
                                                        <label class="font-weight-bold small text-uppercase active" for="edit_name_{{ $email->id }}">Nome / Entità</label>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <input type="email" class="form-control" name="email_address" id="edit_email_address_{{ $email->id }}" value="{{ $email->email_address }}" required>
                                                        <label class="font-weight-bold small text-uppercase active" for="edit_email_address_{{ $email->id }}">Indirizzo Email</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 form-group">
                                                        <div class="select-wrapper">
                                                            <select class="form-control" name="role_type" id="edit_role_type_{{ $email->id }}" required>
                                                                <option value="soup" {{ $email->role_type == 'soup' ? 'selected' : '' }}>S.O.U.P.</option>
                                                                <option value="cop" {{ $email->role_type == 'cop' ? 'selected' : '' }}>C.O.P.</option>
                                                                <option value="test" {{ $email->role_type == 'test' ? 'selected' : '' }}>Email di Test</option>
                                                            </select>
                                                            <label class="font-weight-bold small text-uppercase active" for="edit_role_type_{{ $email->id }}">Tipo (Ruolo)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <input type="text" class="form-control" name="province" id="edit_province_{{ $email->id }}" maxlength="2" value="{{ $email->province }}" placeholder="Es: CZ">
                                                        <label class="font-weight-bold small text-uppercase active" for="edit_province_{{ $email->id }}">Provincia (Sigla)</label>
                                                    </div>
                                                    <div class="col-md-4 form-group">
                                                        <input type="text" class="form-control" name="municipality" id="edit_municipality_{{ $email->id }}" value="{{ $email->municipality }}">
                                                        <label class="font-weight-bold small text-uppercase active" for="edit_municipality_{{ $email->id }}">Comune</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <textarea class="form-control" name="notes" id="edit_notes_{{ $email->id }}" rows="2">{{ $email->notes }}</textarea>
                                                    <label class="font-weight-bold small text-uppercase active" for="edit_notes_{{ $email->id }}">Note / Dettagli</label>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id="activeEditSwitch{{ $email->id }}" name="is_active" value="1" {{ $email->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label font-weight-bold" for="activeEditSwitch{{ $email->id }}">Attivo (Riceve le mail)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salva Modifiche</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-3 text-gray-300"></i><br>
                                    Nessun indirizzo email configurato. Aggiungine uno per iniziare.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addEmailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('admin.emails.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle"></i> Nuovo Destinatario</h5>
                        <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <input type="text" class="form-control" name="name" id="add_name" required placeholder="Es. SOUP Regionale Calabria">
                                <label class="font-weight-bold small text-uppercase" for="add_name">Nome / Entità</label>
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="email" class="form-control" name="email_address" id="add_email_address" required placeholder="email@dominio.it">
                                <label class="font-weight-bold small text-uppercase" for="add_email_address">Indirizzo Email</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <div class="select-wrapper">
                                    <select class="form-control" name="role_type" id="add_role_type" required>
                                        <option value="soup">S.O.U.P.</option>
                                        <option value="cop">C.O.P.</option>
                                        <option value="test">Email di Test</option>
                                    </select>
                                    <label class="font-weight-bold small text-uppercase active" for="add_role_type">Tipo (Ruolo)</label>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <input type="text" class="form-control" name="province" id="add_province" maxlength="2" placeholder="Es: CZ">
                                <label class="font-weight-bold small text-uppercase" for="add_province">Provincia (Sigla)</label>
                            </div>
                            <div class="col-md-4 form-group">
                                <input type="text" class="form-control" name="municipality" id="add_municipality" placeholder="Es. Catanzaro">
                                <label class="font-weight-bold small text-uppercase" for="add_municipality">Comune</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="notes" id="add_notes" rows="2" placeholder="Informazioni opzionali aggiuntive..."></textarea>
                            <label class="font-weight-bold small text-uppercase" for="add_notes">Note / Dettagli</label>
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="activeSwitch" name="is_active" value="1" checked>
                                <label class="custom-control-label font-weight-bold" for="activeSwitch">Attivo (Riceve le mail)</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Aggiungi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>

