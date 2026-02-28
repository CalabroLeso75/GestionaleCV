<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Postazioni AIB (Antincendio Boschivo)</span>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStationModal">
                <i class="fas fa-plus me-2"></i>Nuova Postazione
            </button>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Nome</th>
                                    <th>Tipo</th>
                                    <th>Provincia</th>
                                    <th>Comune</th>
                                    <th>Coordinate (DMS)</th>
                                    <th>Stato</th>
                                    <th class="text-end pe-4">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stations as $station)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $station->nome }}</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2">
                                            {{ $station->tipo }}
                                        </span>
                                    </td>
                                    <td>{{ $station->provincia }}</td>
                                    <td>{{ $station->comune }}</td>
                                    <td class="small font-monospace text-muted">
                                        {{ $station->lat_dms }}<br>{{ $station->lon_dms }}
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill {{ $station->stato === 'Attivo' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $station->stato }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-info ms-1"><i class="fas fa-map-marker-alt"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        Nessuna postazione registrata.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nuova Postazione -->
    <div class="modal fade" id="addStationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('pc.aib.stations.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Aggiungi Postazione AIB</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Nome Postazione</label>
                                <input type="text" name="nome" class="form-control" required placeholder="es. Base AIB Camigliatello">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tipo</label>
                                <select name="tipo" class="form-select">
                                    <option>Base Operativa</option>
                                    <option>Vedetta</option>
                                    <option>Eliporto</option>
                                    <option>Altro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Provincia</label>
                                <select name="provincia" class="form-select" required>
                                    <option value="CS">Cosenza</option>
                                    <option value="CZ">Catanzaro</option>
                                    <option value="KR">Crotone</option>
                                    <option value="RC">Reggio Calabria</option>
                                    <option value="VV">Vibo Valentia</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Comune</label>
                                <input type="text" name="comune" class="form-control">
                            </div>
                            <div class="col-12">
                                <hr>
                                <h6 class="text-muted mb-3">Geolocalizzazione (Coordinate)</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Latitudine (Decimali)</label>
                                <input type="text" name="latitudine" id="lat_dec" class="form-control" placeholder="es. 39.2045">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitudine (Decimali)</label>
                                <input type="text" name="longitudine" id="lon_dec" class="form-control" placeholder="es. 16.2731">
                            </div>
                            <div class="col-12 text-center my-2 text-muted small">oppure</div>
                            <div class="col-md-6">
                                <label class="form-label">Latitudine (DMS)</label>
                                <input type="text" name="lat_dms" class="form-control" placeholder="es. N 39° 12' 34.5\"">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitudine (DMS)</label>
                                <input type="text" name="lon_dms" class="form-control" placeholder="es. E 16° 15' 22.8\"">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Salva Postazione</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
