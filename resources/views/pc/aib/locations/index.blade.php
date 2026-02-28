<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">Gestione Sedi e Postazioni AIB</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLocationModal">
                <i class="fas fa-building me-2"></i>Nuova Sede Aziendale
            </button>
        </div>
    </x-slot>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            @forelse($locations as $location)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-primary">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $location->nome }}
                            @if(!$location->is_active)
                                <span class="badge bg-danger-subtle text-danger ms-2 fs-6">Inattiva</span>
                            @endif
                        </h4>
                        <p class="text-muted mb-0 small">
                            {{ $location->tipo_sede }} | {{ $location->indirizzo ?? 'Nessun indirizzo' }} - {{ $location->citta ?? '' }} ({{ $location->provincia }})
                        </p>
                    </div>
                    <div>
                        <!-- Dropdown Azioni Sede -->
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addStationModal_{{ $location->id }}">
                                        <i class="fas fa-plus text-success me-2"></i> Aggiungi Postazione AIB
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editLocationModal_{{ $location->id }}">
                                        <i class="fas fa-edit text-primary me-2"></i> Modifica Sede
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('pc.aib.locations.destroy', $location->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger" onclick="return confirm('Eliminare questa sede e tutte le postazioni collegate?')">
                                            <i class="fas fa-trash me-2"></i> Elimina Sede
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-body bg-light rounded-bottom">
                    <h6 class="text-uppercase text-muted fw-bold mb-3 small"><i class="fas fa-fire-extinguisher me-2"></i>Postazioni AIB Collegate</h6>
                    
                    @if($location->aibStations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless align-middle bg-white rounded shadow-sm">
                                <thead class="border-bottom">
                                    <tr class="text-muted">
                                        <th class="ps-3 py-2">Nome Postazione</th>
                                        <th>Tipo</th>
                                        <th>Coordinate (DMS)</th>
                                        <th>Stato</th>
                                        <th class="text-end pe-3">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($location->aibStations as $station)
                                    <tr class="border-bottom">
                                        <td class="ps-3 fw-bold">{{ $station->nome }}</td>
                                        <td><span class="badge bg-secondary-subtle text-secondary">{{ $station->tipo }}</span></td>
                                        <td class="font-monospace small text-muted">{{ $station->lat_dms }} <br> {{ $station->lon_dms }}</td>
                                        <td>
                                            @if($station->stato === 'Attivo')
                                                <span class="badge bg-success">Attivo</span>
                                            @elseif($station->stato === 'In Manutenzione')
                                                <span class="badge bg-warning text-dark">Manutenzione</span>
                                            @else
                                                <span class="badge bg-danger">Inattivo</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <button class="btn btn-sm btn-link text-primary" data-bs-toggle="modal" data-bs-target="#editStationModal_{{ $station->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('pc.aib.stations.destroy', $station->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-link text-danger" onclick="return confirm('Eliminare la postazione?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modals for this Station -->
                                    <div class="modal fade" id="editStationModal_{{ $station->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('pc.aib.stations.update', $station->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Modifica Postazione AIB</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nome Postazione *</label>
                                                            <input type="text" name="nome" value="{{ $station->nome }}" class="form-control" required>
                                                        </div>
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label">Tipo *</label>
                                                                <select name="tipo" class="form-select" required>
                                                                    <option value="Base Operativa" {{ $station->tipo=='Base Operativa'?'selected':'' }}>Base Operativa</option>
                                                                    <option value="Vedetta" {{ $station->tipo=='Vedetta'?'selected':'' }}>Vedetta</option>
                                                                    <option value="Eliporto" {{ $station->tipo=='Eliporto'?'selected':'' }}>Eliporto</option>
                                                                    <option value="Altro" {{ $station->tipo=='Altro'?'selected':'' }}>Altro</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label">Stato *</label>
                                                                <select name="stato" class="form-select" required>
                                                                    <option value="Attivo" {{ $station->stato=='Attivo'?'selected':'' }}>Attivo</option>
                                                                    <option value="Inattivo" {{ $station->stato=='Inattivo'?'selected':'' }}>Inattivo</option>
                                                                    <option value="In Manutenzione" {{ $station->stato=='In Manutenzione'?'selected':'' }}>In Manutenzione</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label">Latitudine (DMS)</label>
                                                                <input type="text" name="lat_dms" value="{{ $station->lat_dms }}" class="form-control">
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label">Longitudine (DMS)</label>
                                                                <input type="text" name="lon_dms" value="{{ $station->lon_dms }}" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Note</label>
                                                            <textarea name="note" class="form-control" rows="2">{{ $station->note }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                                                        <button type="submit" class="btn btn-primary">Salva Modifiche</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-light text-center border text-muted py-3 mb-0">
                            Nessuna postazione AIB ancora collegata a questa sede.
                            <button class="btn btn-sm btn-link text-primary" data-bs-toggle="modal" data-bs-target="#addStationModal_{{ $location->id }}">Aggiungi Subito</button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modals for this Location -->
            
            <!-- Create Station Modal -->
            <div class="modal fade" id="addStationModal_{{ $location->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('pc.aib.stations.store', $location->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Aggiungi Postazione AIB a {{ $location->nome }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nome Postazione *</label>
                                    <input type="text" name="nome" class="form-control" placeholder="Es. Squadra Cosenza 1" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Tipo *</label>
                                        <select name="tipo" class="form-select" required>
                                            <option value="Base Operativa">Base Operativa</option>
                                            <option value="Vedetta">Vedetta</option>
                                            <option value="Eliporto">Eliporto</option>
                                            <option value="Altro">Altro</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Stato *</label>
                                        <select name="stato" class="form-select" required>
                                            <option value="Attivo">Attivo</option>
                                            <option value="Inattivo">Inattivo</option>
                                            <option value="In Manutenzione">In Manutenzione</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Latitudine (DMS)</label>
                                        <input type="text" name="lat_dms" class="form-control" placeholder="es. N 39° 12' 34.5\"">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Longitudine (DMS)</label>
                                        <input type="text" name="lon_dms" class="form-control" placeholder="es. E 16° 15' 22.8\"">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Note</label>
                                    <textarea name="note" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-primary">Salva Postazione</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Location Modal -->
            <div class="modal fade" id="editLocationModal_{{ $location->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('pc.aib.locations.update', $location->id) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Modifica Sede Aziendale</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold">Nome Struttura Sede *</label>
                                        <input type="text" name="nome" value="{{ $location->nome }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tipo Sede *</label>
                                        <select name="tipo_sede" class="form-select" required>
                                            <option {{ $location->tipo_sede == 'Distretto' ? 'selected' : '' }}>Distretto</option>
                                            <option {{ $location->tipo_sede == 'Magazzino' ? 'selected' : '' }}>Magazzino</option>
                                            <option {{ $location->tipo_sede == 'Ufficio' ? 'selected' : '' }}>Ufficio</option>
                                            <option {{ $location->tipo_sede == 'Officina' ? 'selected' : '' }}>Officina</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Indirizzo</label>
                                        <input type="text" name="indirizzo" value="{{ $location->indirizzo }}" class="form-control">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Città</label>
                                        <input type="text" name="citta" value="{{ $location->citta }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Provincia *</label>
                                        <select name="provincia" class="form-select" required>
                                            <option value="CS" {{ $location->provincia == 'CS'?'selected':'' }}>Cosenza</option>
                                            <option value="CZ" {{ $location->provincia == 'CZ'?'selected':'' }}>Catanzaro</option>
                                            <option value="KR" {{ $location->provincia == 'KR'?'selected':'' }}>Crotone</option>
                                            <option value="RC" {{ $location->provincia == 'RC'?'selected':'' }}>Reggio Calabria</option>
                                            <option value="VV" {{ $location->provincia == 'VV'?'selected':'' }}>Vibo Valentia</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">CAP</label>
                                        <input type="text" name="cap" value="{{ $location->cap }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-primary">Aggiorna Sede</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @empty
            <div class="text-center py-5 text-muted bg-white rounded shadow-sm">
                <i class="fas fa-building fa-3x mb-3 text-light"></i>
                <p class="mb-0">Nessuna Sede inserita nel sistema.</p>
                <button type="button" class="btn btn-outline-primary mt-3" data-bs-toggle="modal" data-bs-target="#createLocationModal">
                    Crea la Prima Sede
                </button>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Create Location Modal -->
    <div class="modal fade" id="createLocationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('pc.aib.locations.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nuova Sede Aziendale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Nome Struttura Sede *</label>
                                <input type="text" name="nome" class="form-control" placeholder="Es. Distretto di Rende" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo Sede *</label>
                                <select name="tipo_sede" class="form-select" required>
                                    <option>Distretto</option>
                                    <option>Magazzino</option>
                                    <option>Ufficio</option>
                                    <option>Officina</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Indirizzo</label>
                                <input type="text" name="indirizzo" class="form-control">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Città</label>
                                <input type="text" name="citta" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Provincia *</label>
                                <select name="provincia" class="form-select" required>
                                    <option value="CS">Cosenza</option>
                                    <option value="CZ">Catanzaro</option>
                                    <option value="KR">Crotone</option>
                                    <option value="RC">Reggio Calabria</option>
                                    <option value="VV">Vibo Valentia</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">CAP</label>
                                <input type="text" name="cap" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Salva Sede</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
