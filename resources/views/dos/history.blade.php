<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold mb-0">
                {{ __('Storico Rilevazioni DOS / Emergenze') }}
            </h2>
            <x-back-button />
        </div>
    </x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white text-primary fw-bold">
                <svg class="icon icon-sm me-2" aria-hidden="true"><use href="/sprites.php#it-list"></use></svg>
                Archivio Rilevazioni
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 text-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Data / Ora</th>
                                <th>Operatore</th>
                                <th>Ruolo</th>
                                <th>Comune (PR)</th>
                                <th>Toponimo</th>
                                <th>Coordinate</th>
                                <th>GIS</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <td class="align-middle fw-bold">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="align-middle">
                                        {{ $report->user->name ?? 'N/D' }} {{ $report->user->surname ?? '' }}
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge {{ $report->role_snapshot === 'DOS' ? 'bg-danger' : 'bg-primary' }}">
                                            {{ $report->role_snapshot }}
                                        </span>
                                    </td>
                                    <td class="align-middle">{{ $report->municipality ?: 'N/D' }} {{ $report->province ? '(' . $report->province . ')' : '' }}</td>
                                    <td class="align-middle">{{ $report->toponym ?: 'N/D' }}</td>
                                    <td class="align-middle">
                                        Lat: {{ number_format($report->fire_lat, 5) }}<br>
                                        Lng: {{ number_format($report->fire_lng, 5) }}
                                    </td>
                                    <td class="align-middle">
                                        @if($report->area_hectares) Area: {{ $report->area_hectares }} ha <br> @endif
                                        @if($report->front_meters) Fronte: ~{{ $report->front_meters }} m <br> @endif
                                        @if($report->kml_path) <a href="{{ asset('storage/' . $report->kml_path) }}" class="btn btn-xs btn-outline-success" download>KML</a> @endif
                                    </td>
                                    <td class="align-middle">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-{{ $report->id }}">
                                            <svg class="icon icon-xs" aria-hidden="true"><use href="/sprites.php#it-zoom-in"></use></svg> Dettagli
                                        </button>

                                        <!-- Modal Dettaglio -->
                                        <div class="modal fade" id="modal-{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                          <div class="modal-dialog modal-lg">
                                            <div class="modal-content border-0 shadow-lg">
                                              <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="fas fa-info-circle me-2"></i>Dettaglio Emergenza #{{ $report->id }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                              </div>
                                              <div class="modal-body p-4 text-sm">
                                                <div class="row">
                                                    <!-- Colonna 1: Localizzazione -->
                                                    <div class="col-md-6 border-end">
                                                        <h6 class="text-primary fw-bold text-uppercase small mb-3 border-bottom pb-1">
                                                            <i class="fas fa-map-marker-alt me-1"></i> Localizzazione
                                                        </h6>
                                                        <ul class="list-unstyled mb-4">
                                                            <li class="mb-2"><strong>Data/Ora:</strong> {{ $report->created_at->format('d/m/Y H:i') }}</li>
                                                            <li class="mb-2"><strong>Comune:</strong> {{ $report->municipality ?: 'N/D' }} ({{ $report->province ?: 'ND' }})</li>
                                                            <li class="mb-2"><strong>Toponimo:</strong> <span class="text-info">{{ $report->toponym ?: 'N/D' }}</span></li>
                                                            <li class="mb-2">
                                                                <strong>Coordinate:</strong>
                                                                <code class="bg-light p-1 rounded">{{ number_format($report->fire_lat, 5) }}, {{ number_format($report->fire_lng, 5) }}</code>
                                                                <a href="https://www.google.com/maps?q={{ $report->fire_lat }},{{ $report->fire_lng }}" target="_blank" class="ms-2 btn btn-xs btn-outline-secondary py-0">
                                                                    <i class="fas fa-external-link-alt"></i> Vedi Mappa
                                                                </a>
                                                            </li>
                                                            <li class="mb-2"><strong>Distanza da Operatore:</strong> {{ $report->distance ? number_format($report->distance) . " metri" : "N/D" }}</li>
                                                        </ul>

                                                        <h6 class="text-success fw-bold text-uppercase small mb-3 border-bottom pb-1">
                                                            <i class="fas fa-drafting-compass me-1"></i> Analisi GIS
                                                        </h6>
                                                        <ul class="list-unstyled">
                                                            @if($report->area_hectares) <li class="mb-2 text-success"><strong>Area Stimata:</strong> {{ $report->area_hectares }} Ha</li> @endif
                                                            @if($report->front_meters) <li class="mb-2 text-danger"><strong>Fronte Fiamma:</strong> ~{{ $report->front_meters }} m</li> @endif
                                                            <li class="mt-3">
                                                                @if($report->kml_path)
                                                                    <a href="{{ asset('storage/' . $report->kml_path) }}" class="btn btn-sm btn-success w-100" download>
                                                                        <i class="fas fa-download me-2"></i> Scarica File Perimetro (KML)
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted small italic">Nessun dato vettoriale disponibile</span>
                                                                @endif
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <!-- Colonna 2: Meteo & Note -->
                                                    <div class="col-md-6 ps-md-4">
                                                        <h6 class="text-warning fw-bold text-uppercase small mb-3 border-bottom pb-1">
                                                            <i class="fas fa-wind me-1"></i> Situazione Meteo
                                                        </h6>
                                                        <div class="bg-light p-3 rounded mb-4">
                                                            <div class="mb-2"><strong>Attuale:</strong> {{ $report->temperature ? $report->temperature . "°C" : "N/D" }} | {{ $report->wind_speed ? $report->wind_speed . " km/h" : "N/D" }} (Dir: {{ $report->wind_direction ?: "N/D" }})</div>
                                                            <div class="small text-muted mb-1 border-top pt-2">Forecast prossime ore:</div>
                                                            <div class="row text-center mt-2">
                                                                <div class="col-4 px-1">
                                                                    <div class="fw-bold">+2h</div>
                                                                    <div style="font-size: 0.75rem;">{{ $report->wind_forecast_2h_speed ?: '-' }} km/h</div>
                                                                    <div class="badge bg-secondary" style="font-size: 0.6rem;">{{ $report->wind_forecast_2h_dir }}</div>
                                                                </div>
                                                                <div class="col-4 px-1">
                                                                    <div class="fw-bold">+4h</div>
                                                                    <div style="font-size: 0.75rem;">{{ $report->wind_forecast_4h_speed ?: '-' }} km/h</div>
                                                                    <div class="badge bg-secondary" style="font-size: 0.6rem;">{{ $report->wind_forecast_4h_dir }}</div>
                                                                </div>
                                                                <div class="col-4 px-1">
                                                                    <div class="fw-bold">+6h</div>
                                                                    <div style="font-size: 0.75rem;">{{ $report->wind_forecast_6h_speed ?: '-' }} km/h</div>
                                                                    <div class="badge bg-secondary" style="font-size: 0.6rem;">{{ $report->wind_forecast_6h_dir }}</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <h6 class="text-secondary fw-bold text-uppercase small mb-3 border-bottom pb-1">
                                                            <i class="fas fa-sticky-note me-1"></i> Note Operative
                                                        </h6>
                                                        <div class="p-2 bg-light border-start border-4 border-secondary border-opacity-25 rounded-end italic" style="min-height: 80px; font-style: italic;">
                                                            {{ $report->notes ?: 'Nessun commento aggiuntivo inserito.' }}
                                                        </div>

                                                        <div class="mt-4 text-end text-muted x-small" style="font-size:0.7rem;">
                                                            Inviato da: {{ $report->user->name ?? 'N/D' }} {{ $report->user->surname ?? '' }}<br>
                                                            Identificativo Report: #{{ $report->id }}
                                                        </div>
                                                    </div>
                                                </div>
                                              </div>
                                              <div class="modal-footer bg-light border-0">
                                                <button type="button" class="btn btn-primary px-4 fw-bold" data-bs-dismiss="modal">Chiudi</button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Nessuna rilevazione salvata nello storico.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
