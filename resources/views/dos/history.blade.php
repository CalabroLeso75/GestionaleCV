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
                                            <div class="modal-content">
                                              <div class="modal-header">
                                                <h5 class="modal-title">Dettaglio Emergenza #{{ $report->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                              </div>
                                              <div class="modal-body">
                                                <div class="row text-sm">
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Data:</strong> {{ $report->created_at->format('d/m/Y H:i') }}<br>
                                                        <strong>Segnalante:</strong> {{ $report->user->name ?? 'N/D' }} {{ $report->user->surname ?? '' }} ({{ $report->role_snapshot }})<br>
                                                        <strong>Distanza da Operatore:</strong> {{ $report->distance ? $report->distance . " metri" : "N/D" }}<br>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <strong>Meteo Attuale:</strong> {{ $report->temperature ? $report->temperature . "°C" : "N/D" }} | {{ $report->wind_speed ? $report->wind_speed . " km/h" : "N/D" }} (Dir: {{ $report->wind_direction ?: "N/D" }})<br>
                                                        <strong>Previsione +2h:</strong> {{ $report->wind_forecast_2h_speed ? $report->wind_forecast_2h_speed . " km/h (Raff: {$report->wind_forecast_2h_gust})" : "N/D" }}<br>
                                                        <strong>Previsione +4h:</strong> {{ $report->wind_forecast_4h_speed ? $report->wind_forecast_4h_speed . " km/h (Raff: {$report->wind_forecast_4h_gust})" : "N/D" }}<br>
                                                        <strong>Previsione +6h:</strong> {{ $report->wind_forecast_6h_speed ? $report->wind_forecast_6h_speed . " km/h (Raff: {$report->wind_forecast_6h_gust})" : "N/D" }}<br>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <strong>Note:</strong>
                                                        <p class="border p-2 bg-light">{{ $report->notes ?: 'Nessuna nota aggiuntiva' }}</p>
                                                    </div>
                                                </div>
                                              </div>
                                              <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
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
