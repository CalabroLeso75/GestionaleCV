<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h2 class="h4 font-weight-bold text-dark mb-0">Gestione Sedi e Postazioni AIB</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLocationModal">
                <i class="fas fa-building me-2"></i>Nuova Sede Aziendale
            </button>
        </div>
    </x-slot>

    <!-- Main Content -->
    
    <!-- Filtri di Ricerca -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body py-3">
            <form action="{{ route('pc.aib.locations.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Tipo Struttura</label>
                    <select name="tipo_sede" class="form-select form-select-sm">
                        <option value="">Tutti i tipi</option>
                        <option value="Sede Centrale" {{ request('tipo_sede') == 'Sede Centrale' ? 'selected' : '' }}>Sede Centrale</option>
                        <option value="Distretto" {{ request('tipo_sede') == 'Distretto' ? 'selected' : '' }}>Distretto</option>
                        <option value="Distaccamento" {{ request('tipo_sede') == 'Distaccamento' ? 'selected' : '' }}>Distaccamento</option>
                        <option value="Magazzino" {{ request('tipo_sede') == 'Magazzino' ? 'selected' : '' }}>Magazzino</option>
                        <option value="Officina" {{ request('tipo_sede') == 'Officina' ? 'selected' : '' }}>Officina</option>
                        <option value="Parco Macchine" {{ request('tipo_sede') == 'Parco Macchine' ? 'selected' : '' }}>Parco Macchine</option>
                        <option value="Sala Operativa" {{ request('tipo_sede') == 'Sala Operativa' ? 'selected' : '' }}>Sala Operativa</option>
                        <option value="Ufficio" {{ request('tipo_sede') == 'Ufficio' ? 'selected' : '' }}>Ufficio</option>
                        <option value="Vivaio" {{ request('tipo_sede') == 'Vivaio' ? 'selected' : '' }}>Vivaio</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Provincia</label>
                    <select name="provincia" id="filterProvincia" class="form-select form-select-sm proxy-province-select" data-target="filterCitta">
                        <option value="">Tutte le province</option>
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->short_code }}" data-id="{{ $prov->id }}" {{ request('provincia') == $prov->short_code ? 'selected' : '' }}>{{ $prov->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Comune</label>
                    <input type="text" name="citta" class="form-control form-control-sm" value="{{ request('citta') }}" placeholder="Cerca comune...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Filtri Aggiuntivi</label>
                    <div class="form-check form-switch pt-1">
                        <input class="form-check-input" type="checkbox" name="has_aib_stations" id="hasAibStations" value="1" {{ request()->has('has_aib_stations') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="hasAibStations">Solo con postazioni AIB</label>
                    </div>
                </div>
                <div class="col-md-3 mt-3 mt-md-0 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1"><i class="fas fa-search me-1"></i> Filtra</button>
                    @if(request()->hasAny(['tipo_sede', 'provincia', 'citta', 'has_aib_stations']))
                        <a href="{{ route('pc.aib.locations.index') }}" class="btn btn-sm btn-light flex-grow-1 text-muted"><i class="fas fa-times me-1"></i> Annulla</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

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
                            {{ $location->tipo_sede }} | {{ $location->indirizzo ?? 'Nessun indirizzo' }} 
                            @if($location->localita) - Loc. {{ $location->localita }} @endif
                            - {{ $location->citta ?? '' }} ({{ $location->provincia }})
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
                                        <td class="ps-3 fw-bold">
                                            {{ $station->nome }}
                                            @if($station->localita || $station->comune)
                                            <div class="small fw-normal text-muted">
                                                <i class="fas fa-map-marker-alt" style="font-size:0.8em"></i> {{ $station->localita ? 'Loc. '.$station->localita : '' }} {{ $station->comune ? '('.$station->comune.')' : '' }}
                                            </div>
                                            @endif
                                        </td>
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
                                                <form action="{{ route('pc.aib.stations.update', $station->id) }}" method="POST" onkeydown="return event.key != 'Enter';">
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
                                                            <div class="col-4">
                                                                <label class="form-label">Provincia (Filtro)</label>
                                                                <select class="form-select province-select" data-target="cityEditStation{{ $station->id }}">
                                                                    <option value="">Scegli...</option>
                                                                    @foreach($provinces as $prov)
                                                                        <option value="{{ $prov->short_code }}" data-id="{{ $prov->id }}" {{ $location->provincia == $prov->short_code ? 'selected' : '' }}>{{ $prov->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label">Comune</label>
                                                                <select name="comune" id="cityEditStation{{ $station->id }}" class="form-select auto-search-map" data-map-suffix="EditStation{{ $station->id }}">
                                                                    @if($station->comune)
                                                                    <option value="{{ $station->comune }}" selected>{{ $station->comune }}</option>
                                                                    @elseif($location->citta)
                                                                    <option value="{{ $location->citta }}" selected>{{ $location->citta }}</option>
                                                                    @else
                                                                    <option value="">Seleziona Provincia prima</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label">Località</label>
                                                                <input type="text" name="localita" value="{{ $station->localita }}" class="form-control" id="addrEditStation{{ $station->id }}" class="auto-search-map" data-map-suffix="EditStation{{ $station->id }}">
                                                            </div>
                                                        </div>
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-4">
                                                                <label class="form-label">Lat (Decimale)</label>
                                                                <input type="text" name="latitudine" id="latEditStation{{ $station->id }}" value="{{ $station->latitudine }}" class="form-control" placeholder="Es. 38.9">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label">Lng (Decimale)</label>
                                                                <input type="text" name="longitudine" id="lngEditStation{{ $station->id }}" value="{{ $station->longitudine }}" class="form-control" placeholder="Es. 16.5">
                                                            </div>
                                                            <div class="col-4">
                                                                <label class="form-label">Coordinate DMS</label>
                                                                <input type="text" name="lat_dms" value="{{ $station->lat_dms }}" class="form-control" placeholder="Es. N 39°...">
                                                            </div>
                                                        </div>
                                                        <div class="row mt-2 mb-3">
                                                            <div class="col-12">
                                                                <label class="form-label d-flex justify-content-between align-items-center">
                                                                    <span><i class="fas fa-map-marked-alt text-primary me-2"></i> Posizione sulla Mappa</span>
                                                                    <small class="text-muted fw-normal"><i class="fas fa-magic me-1"></i> Ricerca automatica tramite comune/località</small>
                                                                </label>
                                                                <div id="mapEditStation{{ $station->id }}" style="height: 200px; width: 100%; border-radius: 4px; border: 1px solid #ced4da; z-index: 1;"></div>
                                                                <div class="mt-2 bg-light p-2 border rounded">
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="text" id="reverseAddressEditStation{{ $station->id }}" class="form-control bg-white" readonly placeholder="Trascina il marker...">
                                                                        <button class="btn btn-primary btn-sm" type="button" onclick="useAddress('EditStation{{ $station->id }}', true)">
                                                                            <i class="fas fa-check"></i> Usa
                                                                        </button>
                                                                    </div>
                                                                </div>
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
                        <form action="{{ route('pc.aib.stations.store', $location->id) }}" method="POST" onkeydown="return event.key != 'Enter';">
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
                                    <div class="col-4">
                                        <label class="form-label">Provincia (Filtro)</label>
                                        <select class="form-select province-select" data-target="cityAddStation{{ $location->id }}">
                                            <option value="">Scegli...</option>
                                            @foreach($provinces as $prov)
                                                <option value="{{ $prov->short_code }}" data-id="{{ $prov->id }}" {{ $location->provincia == $prov->short_code ? 'selected' : '' }}>{{ $prov->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label">Comune</label>
                                        <select name="comune" id="cityAddStation{{ $location->id }}" class="form-select auto-search-map" data-map-suffix="AddStation{{ $location->id }}">
                                            @if($location->citta)
                                            <option value="{{ $location->citta }}" selected>{{ $location->citta }}</option>
                                            @else
                                            <option value="">Seleziona Provincia prima</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label">Località</label>
                                        <input type="text" name="localita" class="form-control" id="addrAddStation{{ $location->id }}" class="auto-search-map" data-map-suffix="AddStation{{ $location->id }}">
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-4">
                                        <label class="form-label">Lat (Decimale)</label>
                                        <input type="text" name="latitudine" id="latAddStation{{ $location->id }}" class="form-control" placeholder="Es. 38.9">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label">Lng (Decimale)</label>
                                        <input type="text" name="longitudine" id="lngAddStation{{ $location->id }}" class="form-control" placeholder="Es. 16.5">
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label">Coordinate DMS</label>
                                        <input type="text" name="lat_dms" class="form-control" placeholder="Es. N 39°...">
                                    </div>
                                </div>
                                <div class="row mt-2 mb-3">
                                    <div class="col-12">
                                        <label class="form-label d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-map-marked-alt text-primary me-2"></i> Posizione sulla Mappa</span>
                                            <small class="text-muted fw-normal"><i class="fas fa-magic me-1"></i> Ricerca automatica tramite comune/località</small>
                                        </label>
                                        <div id="mapAddStation{{ $location->id }}" style="height: 200px; width: 100%; border-radius: 4px; border: 1px solid #ced4da; z-index: 1;"></div>
                                        <div class="mt-2 bg-light p-2 border rounded">
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="reverseAddressAddStation{{ $location->id }}" class="form-control bg-white" readonly placeholder="Trascina il marker...">
                                                <button class="btn btn-primary btn-sm" type="button" onclick="useAddress('AddStation{{ $location->id }}', true)">
                                                    <i class="fas fa-check"></i> Usa
                                                </button>
                                            </div>
                                        </div>
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
                        <form action="{{ route('pc.aib.locations.update', $location->id) }}" method="POST" onkeydown="return event.key != 'Enter';">
                            @csrf @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Modifica Sede Aziendale</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-9">
                                        <label class="form-label fw-bold">Nome Struttura Sede *</label>
                                        <input type="text" name="nome" value="{{ $location->nome }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tipo Sede *</label>
                                        <select name="tipo_sede" class="form-select" required>
                                            <option value="Sede Centrale" {{ $location->tipo_sede == 'Sede Centrale' ? 'selected' : '' }}>Sede Centrale</option>
                                            <option value="Distretto" {{ $location->tipo_sede == 'Distretto' ? 'selected' : '' }}>Distretto</option>
                                            <option value="Distaccamento" {{ $location->tipo_sede == 'Distaccamento' ? 'selected' : '' }}>Distaccamento</option>
                                            <option value="Magazzino" {{ $location->tipo_sede == 'Magazzino' ? 'selected' : '' }}>Magazzino</option>
                                            <option value="Officina" {{ $location->tipo_sede == 'Officina' ? 'selected' : '' }}>Officina</option>
                                            <option value="Parco Macchine" {{ $location->tipo_sede == 'Parco Macchine' ? 'selected' : '' }}>Parco Macchine</option>
                                            <option value="Sala Operativa" {{ $location->tipo_sede == 'Sala Operativa' ? 'selected' : '' }}>Sala Operativa</option>
                                            <option value="Ufficio" {{ $location->tipo_sede == 'Ufficio' ? 'selected' : '' }}>Ufficio</option>
                                            <option value="Vivaio" {{ $location->tipo_sede == 'Vivaio' ? 'selected' : '' }}>Vivaio</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Provincia *</label>
                                        <select name="provincia" class="form-select province-select" data-target="cityEdit{{ $location->id }}" required>
                                            <option value="">Scegli...</option>
                                            @foreach($provinces as $prov)
                                                <option value="{{ $prov->short_code }}" data-id="{{ $prov->id }}" {{ $location->provincia == $prov->short_code ? 'selected' : '' }}>{{ $prov->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Città/Comune *</label>
                                        <select name="citta" id="cityEdit{{ $location->id }}" class="form-select" required>
                                            @if($location->citta)
                                            <option value="{{ $location->citta }}" selected>{{ $location->citta }}</option>
                                            @else
                                            <option value="">Seleziona Provincia prima</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Località</label>
                                        <input type="text" name="localita" value="{{ $location->localita }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">CAP *</label>
                                        <input type="text" name="cap" value="{{ $location->cap }}" class="form-control" required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Indirizzo Fisico (solo testo)</label>
                                        <input type="text" name="indirizzo" id="addrEdit{{ $location->id }}" value="{{ $location->indirizzo }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Latitudine *</label>
                                        <input type="text" name="lat" id="latEdit{{ $location->id }}" value="{{ $location->lat }}" class="form-control" placeholder="Es. 39.12345" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Longitudine *</label>
                                        <input type="text" name="lng" id="lngEdit{{ $location->id }}" value="{{ $location->lng }}" class="form-control" placeholder="Es. 16.54321" required>
                                    </div>
                                    
                                    <div class="col-12 mt-3">
                                        <label class="form-label d-flex justify-content-between align-items-center mb-1">
                                            <span><i class="fas fa-map-marked-alt text-primary me-2"></i> Posizione sulla Mappa</span>
                                        </label>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                            <input type="text" id="mapSearch{{ $location->id }}" class="form-control auto-search-map" data-map-suffix="{{ $location->id }}" placeholder="Cerca indirizzo, PDI o incolla coordinate...">
                                        </div>
                                        <div id="mapEditLocation{{ $location->id }}" style="height: 250px; width: 100%; border-radius: 4px; border: 1px solid #ced4da; z-index: 1;"></div>
                                        
                                        <div class="mt-2 bg-light p-2 border rounded">
                                            <label class="form-label small text-muted mb-1">Indirizzo rilevato dalle coordinate</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="reverseAddress{{ $location->id }}" class="form-control bg-white" readonly placeholder="Trascina il marker per rilevare l'indirizzo...">
                                                <button class="btn btn-primary" type="button" onclick="useAddress('{{ $location->id }}')">
                                                    <i class="fas fa-check-circle me-1"></i> Usa Indirizzo
                                                </button>
                                            </div>
                                        </div>
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
                <form action="{{ route('pc.aib.locations.store') }}" method="POST" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Nuova Sede Aziendale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-9">
                                <label class="form-label fw-bold">Nome Struttura Sede *</label>
                                <input type="text" name="nome" class="form-control" placeholder="Es. Distretto di Rende" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tipo Sede *</label>
                                <select name="tipo_sede" class="form-select" required>
                                    <option value="" selected disabled>Scegli...</option>
                                    <option value="Sede Centrale">Sede Centrale</option>
                                    <option value="Distretto">Distretto</option>
                                    <option value="Distaccamento">Distaccamento</option>
                                    <option value="Magazzino">Magazzino</option>
                                    <option value="Officina">Officina</option>
                                    <option value="Parco Macchine">Parco Macchine</option>
                                    <option value="Sala Operativa">Sala Operativa</option>
                                    <option value="Ufficio">Ufficio</option>
                                    <option value="Vivaio">Vivaio</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">Provincia *</label>
                                <select name="provincia" class="form-select province-select" data-target="cityCreate" required>
                                    <option value="">Scegli...</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov->short_code }}" data-id="{{ $prov->id }}">{{ $prov->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Città/Comune *</label>
                                <select name="citta" id="cityCreate" class="form-select" required>
                                    <option value="">Seleziona prima la provincia</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Località</label>
                                <input type="text" name="localita" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">CAP *</label>
                                <input type="text" name="cap" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Indirizzo Fisico (solo testo)</label>
                                <input type="text" name="indirizzo" id="addrCreate" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Latitudine *</label>
                                <input type="text" name="lat" id="latCreate" class="form-control" placeholder="Es. 39.12345" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Longitudine *</label>
                                <input type="text" name="lng" id="lngCreate" class="form-control" placeholder="Es. 16.54321" required>
                            </div>
                            
                            <div class="col-12 mt-3">
                                <label class="form-label d-flex justify-content-between align-items-center mb-1">
                                    <span><i class="fas fa-map-marked-alt text-primary me-2"></i> Posizione sulla Mappa</span>
                                </label>
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" id="mapSearchCreate" class="form-control auto-search-map" data-map-suffix="Create" placeholder="Cerca indirizzo, PDI o incolla coordinate...">
                                </div>
                                <div id="mapCreateLocation" style="height: 250px; width: 100%; border-radius: 4px; border: 1px solid #ced4da; z-index: 1;"></div>
                                
                                <div class="mt-2 bg-light p-2 border rounded">
                                    <label class="form-label small text-muted mb-1">Indirizzo rilevato dalle coordinate</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="reverseAddressCreate" class="form-control bg-white" readonly placeholder="Trascina il marker per rilevare l'indirizzo...">
                                        <button class="btn btn-primary" type="button" onclick="useAddress('Create')">
                                            <i class="fas fa-check-circle me-1"></i> Usa Indirizzo
                                        </button>
                                    </div>
                                </div>
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
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css' rel='stylesheet' />
    <script>
        // Global references to maps and markers
        var maps = {};
        var markers = {};
        var currentAddresses = {}; // Store current address details by suffix

        function initMap(mapId, suffix, latInputId, lngInputId, defaultLat = 38.90, defaultLng = 16.58) {
            if (maps[suffix]) {
                maps[suffix].invalidateSize();
                return;
            }

            var latVal = document.getElementById(latInputId).value;
            var lngVal = document.getElementById(lngInputId).value;
            var startLat = latVal ? parseFloat(latVal) : defaultLat;
            var startLng = lngVal ? parseFloat(lngVal) : defaultLng;
            var zoom = (latVal && lngVal) ? 15 : 8; 

            // Base Layers
            var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 22,
                maxNativeZoom: 19
            });
            var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri',
                maxZoom: 22,
                maxNativeZoom: 19
            });
            var googleHybridLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                attribution: '&copy; Google',
                maxZoom: 22,
                maxNativeZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            var map = L.map(mapId, {
                center: [startLat, startLng],
                zoom: zoom,
                maxZoom: 22,
                layers: [googleHybridLayer], // default a Google Sat+Labels per avere civici/indirizzi
                fullscreenControl: true,
                fullscreenControlOptions: {
                    position: 'topleft'
                }
            });

            // Layer Control
            var baseMaps = {
                "Google Ibrido (Sat+Indirizzi)": googleHybridLayer,
                "Mappa Stradale (OSM)": osmLayer,
                "Satellitare Puro (Esri)": satelliteLayer
            };
            L.control.layers(baseMaps).addTo(map);

            // Add explicitly unpkg paths for default markers to fix disappearance
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png'
            });

            var marker = L.marker([startLat, startLng], {draggable: true}).addTo(map);

            // Spostare il marker cliccando sulla mappa
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                var position = marker.getLatLng();
                document.getElementById(latInputId).value = position.lat.toFixed(6);
                document.getElementById(lngInputId).value = position.lng.toFixed(6);
                reverseGeocode(position.lat, position.lng, suffix);
            });

            // Update inputs and reverse geocode when marker gets dragged
            marker.on('dragend', function(event) {
                var position = marker.getLatLng();
                document.getElementById(latInputId).value = position.lat.toFixed(6);
                document.getElementById(lngInputId).value = position.lng.toFixed(6);
                reverseGeocode(position.lat, position.lng, suffix);
            });

            maps[suffix] = map;
            markers[suffix] = marker;

            // Fix modal rendering
            setTimeout(function() {
                map.invalidateSize();
                if(latVal && lngVal) {
                    reverseGeocode(startLat, startLng, suffix); // Initial geocode if we have coords
                }
            }, 300);
        }
        // Coordinate Parsing (Decimal + DMS)
        function parseCoordinate(input) {
            var decimalMatch = input.match(/^([-+]?\d{1,2}\.[\d]+)[,\s]+([-+]?\d{1,3}\.[\d]+)$/);
            if (decimalMatch) {
                return { lat: parseFloat(decimalMatch[1]), lon: parseFloat(decimalMatch[2]) };
            }
            
            // Matches formats like 38°34'01.2"N 16°15'28.5"E
            var dmsRegex = /([-+]?\d+)°\s*(\d+)'\s*([\d\.]+)"\s*([NS])\s*[,;\s]*([-+]?\d+)°\s*(\d+)'\s*([\d\.]+)"\s*([EW])/i;
            var dmsMatch = input.match(dmsRegex);
            if (dmsMatch) {
                var lat = Math.abs(parseFloat(dmsMatch[1])) + parseFloat(dmsMatch[2])/60 + parseFloat(dmsMatch[3])/3600;
                if (dmsMatch[4].toUpperCase() === 'S' || parseFloat(dmsMatch[1]) < 0) lat = -lat;
                
                var lon = Math.abs(parseFloat(dmsMatch[5])) + parseFloat(dmsMatch[6])/60 + parseFloat(dmsMatch[7])/3600;
                if (dmsMatch[8].toUpperCase() === 'W' || parseFloat(dmsMatch[5]) < 0) lon = -lon;
                
                return { lat: lat, lon: lon };
            }
            return null;
        }

        // Auto search map based on address + city
        function debounce(func, wait) {
            let timeout;
            return function() {
                var context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }

        function autoSearchMap(suffix) {
            var isCreate = suffix === 'Create';
            var mapSearchTag = document.getElementById(isCreate ? 'mapSearchCreate' : 'mapSearch' + suffix);
            var latInput = document.getElementById(isCreate ? 'latCreate' : 'lat' + suffix) || document.getElementById('latEdit' + suffix);
            var lngInput = document.getElementById(isCreate ? 'lngCreate' : 'lng' + suffix) || document.getElementById('lngEdit' + suffix);
            
            if(!mapSearchTag) return;
            
            var queryParts = [];
            
            // Permettere di inserire le coordinate direttamente nell'indirizzo per fare la ricerca
            if (mapSearchTag && mapSearchTag.value) {
                var coords = parseCoordinate(mapSearchTag.value);
                if (coords) {
                    var map = maps[suffix];
                    var marker = markers[suffix];
                    if (map && marker) {
                        map.setView([coords.lat, coords.lon], 18);
                        marker.setLatLng([coords.lat, coords.lon]);
                        if(latInput) latInput.value = coords.lat.toFixed(6);
                        if(lngInput) lngInput.value = coords.lon.toFixed(6);
                        reverseGeocode(coords.lat, coords.lon, suffix);
                    }
                    return; // Interrompiamo perché è una coordinata, non serve nominatim text search
                }
                
                queryParts.push(mapSearchTag.value);
            }
            
            if(queryParts.length === 0) return;
            
            var queryUrl = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q=' + encodeURIComponent(queryParts[0]);
            
            fetch("{{ route('pc.aib.proxy.nominatim') }}?url=" + encodeURIComponent(queryUrl))
                .then(response => response.json())
                .then(data => {
                    // Evitiamo eccezioni TypeError length of object, controllando che sia un Array
                    if(data && Array.isArray(data) && data.length > 0) {
                        var lat = parseFloat(data[0].lat);
                        var lon = parseFloat(data[0].lon);
                        
                        var map = maps[suffix];
                        var marker = markers[suffix];

                        if(map && marker) {
                            map.setView([lat, lon], 15);
                            marker.setLatLng([lat, lon]);
                            if(latInput) latInput.value = lat.toFixed(6);
                            if(lngInput) lngInput.value = lon.toFixed(6);
                            reverseGeocode(lat, lon, suffix);
                        }
                    } else if (data && data.error) {
                        console.warn("Nominatim Proxy Error:", data.error);
                    }
                })
                .catch(err => console.error("Map Search Fetch Error:", err));
        }

        // Reverse Geocoding
        function reverseGeocode(lat, lng, suffix) {
            var addressInput = document.getElementById('reverseAddress' + suffix);
            if(!addressInput) return;
            
            addressInput.value = "Rilevamento in corso...";
            var queryUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;

            fetch("{{ route('pc.aib.proxy.nominatim') }}?url=" + encodeURIComponent(queryUrl))
                .then(response => response.json())
                .then(data => {
                    if(data && data.address) {
                        var addr = data.address;
                        var parts = [];
                        if (addr.road) parts.push(addr.road);
                        if (addr.house_number) parts.push(addr.house_number);
                        
                        var city = addr.city || addr.town || addr.village || addr.municipality || '';
                        var prov = addr.county || '';
                        var localita = addr.suburb || addr.neighbourhood || addr.hamlet || '';
                        var postcode = addr.postcode || '';
                        
                        var fullString = parts.join(" ") + (city ? ", " + city : "") + (prov ? " (" + prov + ")" : "");
                        addressInput.value = fullString;
                        
                        // Store the object for later use
                        currentAddresses[suffix] = {
                            indirizzo: parts.join(" "),
                            citta: city,
                            cap: postcode,
                            localita: localita
                        };
                    } else if (data && data.error) {
                        console.warn("Reverse Geocoding Proxy Error:", data.error);
                        addressInput.value = "Coordinate non riconosciute";
                    } else {
                        addressInput.value = "Coordinate in area remota/sconosciuta";
                    }
                })
                .catch(err => {
                    console.error("Reverse Geocoding Fetch Error:", err);
                    addressInput.value = "Errore di connessione a OpenStreetMap API";
                });
        }

        // Auto-fill inputs
        function useAddress(suffix) {
            var data = currentAddresses[suffix];
            if (!data) {
                alert("Nessun indirizzo rilevato validato da poter utilizzare.");
                return;
            }
            
            var modalId = "";
            if (suffix === 'Create') modalId = 'createLocationModal';
            else if (suffix.startsWith('EditStation')) modalId = 'editStationModal_' + suffix.replace('EditStation', '');
            else if (suffix.startsWith('AddStation')) modalId = 'addStationModal_' + suffix.replace('AddStation', '');
            else modalId = 'editLocationModal' + suffix;
            
            var modalEl = document.getElementById(modalId);
            if (!modalEl) return;
            
            var cittaInput = modalEl.querySelector('select[name="citta"]') || modalEl.querySelector('select[name="comune"]') || modalEl.querySelector('input[name="citta"]') || modalEl.querySelector('input[name="comune"]');
            var indirizzoInput = modalEl.querySelector('input[name="indirizzo"]');
            var capInput = modalEl.querySelector('input[name="cap"]');
            var locInput = modalEl.querySelector('input[name="localita"]');
            
            if (cittaInput && data.citta) {
                if (cittaInput.tagName.toLowerCase() === 'select') {
                    // Controlla se il comune restituito fa parte dei comuni della provincia
                    var foundOpt = Array.from(cittaInput.options).find(opt => 
                        opt.text.trim().toLowerCase() === data.citta.toLowerCase() || 
                        opt.value.trim().toLowerCase() === data.citta.toLowerCase()
                    );
                    if (foundOpt) {
                        cittaInput.value = foundOpt.value;
                    }
                } else {
                    cittaInput.value = data.citta;
                }
            }
            if (indirizzoInput && data.indirizzo) indirizzoInput.value = data.indirizzo;
            if (capInput && data.cap) capInput.value = data.cap;
            
            // La località deve restare vuota e non popolata da Nominatim
            // (L'utente la scriverà direttamente a mano se necessario)
            
            // Add a brief visual highlight to inputs
            [cittaInput, indirizzoInput, capInput, locInput].forEach(el => {
                if(el){
                    el.classList.add('is-valid');
                    setTimeout(() => el.classList.remove('is-valid'), 1500);
                }
            });
        }

        // Province/City Dependent Select Logic
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.province-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    var targetId = this.getAttribute('data-target');
                    var citySelect = document.getElementById(targetId);
                    if (!citySelect) return;
                    
                    var selectedOption = this.options[this.selectedIndex];
                    var provinceId = selectedOption.getAttribute('data-id');
                    
                    if (!provinceId) {
                        citySelect.innerHTML = '<option value="">Seleziona prima la provincia</option>';
                        return;
                    }
                    
                    citySelect.innerHTML = '<option value="">Caricamento...</option>';
                    
                    fetch('{{ url("api/cities") }}/' + provinceId)
                        .then(response => response.json())
                        .then(data => {
                            citySelect.innerHTML = '<option value="">Seleziona Comune</option>';
                            data.forEach(function(city) {
                                citySelect.innerHTML += `<option value="${city.name}">${city.name}</option>`;
                            });
                        })
                        .catch(err => {
                            console.error('Error fetching cities:', err);
                            citySelect.innerHTML = '<option value="">Errore nel caricamento</option>';
                        });
                });
            });

            // Bind auto-search map to relevant inputs
            document.querySelectorAll('.auto-search-map').forEach(function(el) {
                el.addEventListener('change', debounce(function() {
                    var suffix = this.getAttribute('data-map-suffix');
                    if(suffix) autoSearchMap(suffix);
                }, 800));
                
                if(el.tagName.toLowerCase() === 'input') {
                    el.addEventListener('keyup', debounce(function() {
                        var suffix = this.getAttribute('data-map-suffix');
                        if(suffix) autoSearchMap(suffix);
                    }, 1500));
                }
            });

            var myModals = document.querySelectorAll('.modal');
            myModals.forEach(function(modalEl) {
                modalEl.addEventListener('shown.bs.modal', function (event) {
                    var modalId = event.target.id;
                    
                    if (modalId === 'createLocationModal') {
                        initMap('mapCreateLocation', 'Create', 'latCreate', 'lngCreate');
                    } else if (modalId.startsWith('editLocationModal')) {
                        var id = modalId.replace('editLocationModal', '');
                        initMap('mapEditLocation' + id, id, 'latEdit' + id, 'lngEdit' + id);
                    } else if (modalId.startsWith('editStationModal_')) {
                        var id = modalId.replace('editStationModal_', '');
                        initMap('mapEditStation' + id, 'EditStation' + id, 'latEditStation' + id, 'lngEditStation' + id, 38.90, 16.58);
                    } else if (modalId.startsWith('addStationModal_')) {
                        var id = modalId.replace('addStationModal_', '');
                        initMap('mapAddStation' + id, 'AddStation' + id, 'latAddStation' + id, 'lngAddStation' + id, 38.90, 16.58);
                    }
                });
            });
        });
    </script>
</x-app-layout>
