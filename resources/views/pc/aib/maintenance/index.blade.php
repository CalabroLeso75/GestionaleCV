<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">
                Registro Manutenzioni e Demolizioni AIB
            </h2>
            <button class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#newMaintenanceModal">
                <i class="fas fa-plus me-1"></i> Nuovo Evento M&D
            </button>
        </div>
    </x-slot>

    <div class="row">
        <!-- Dashboard Manutenzione -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-warning h-100">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-tools me-2"></i>Mezzi Attualmente in Manutenzione / Demoliti
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($vehiclesInMaint as $v)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>🚗 {{ $v->marca }} {{ $v->modello }} <span class="badge bg-dark ms-2">{{ $v->targa }}</span></div>
                                <span class="badge {{ $v->stato == 'alienazione' ? 'bg-danger' : 'bg-warning text-dark' }}">{{ strtoupper($v->stato) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-3">Nessun mezzo in officina.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-secondary h-100">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fas fa-mobile-alt me-2"></i>Dispositivi Mobili Attualmente in Riparazione
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($devicesInMaint as $d)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>📱 {{ $d->marca }} {{ $d->modello }} <small class="text-muted ms-2">{{ $d->imei }}</small></div>
                                <span class="badge {{ $d->stato == 'Dismesso' ? 'bg-danger' : 'bg-warning text-dark' }}">{{ strtoupper($d->stato) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center py-3">Nessun dispositivo in riparazione.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-12 mt-2">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-bold text-muted">
                    <i class="fas fa-history me-2"></i>Storico Log Officina e Demolizioni
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">Data Evento</th>
                                <th>Risorsa e Identificativo</th>
                                <th>Evento</th>
                                <th>Responsabile M&D</th>
                                <th>Note Tecniche (Officina)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td class="ps-4 text-nowrap">{{ $log->data_evento->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @if(class_basename($log->asset_type) === 'Vehicle')
                                        🚗 Mezzo: <span class="fw-bold">{{ $log->asset->targa ?? 'N/A' }}</span>
                                    @else
                                        📱 Disp.: <span class="fw-bold">{{ $log->asset->modello ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(str_contains($log->tipo_evento, 'Rientro') || str_contains($log->tipo_evento, 'Reintegro'))
                                        <span class="badge bg-success">{{ $log->tipo_evento }}</span>
                                    @elseif(str_contains($log->tipo_evento, 'Demolizione'))
                                        <span class="badge bg-danger">{{ $log->tipo_evento }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $log->tipo_evento }}</span>
                                    @endif
                                </td>
                                <td class="small">{{ $log->user->name ?? 'Sistema' }} {{ $log->user->surname ?? '' }}</td>
                                <td class="small text-muted">{{ $log->note_officina }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-hammer fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">Nessun movimento di manutenzione in archivio.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <!-- Modale Nuovo Evento Manutenzione / Demolizione -->
    <div class="modal fade" id="newMaintenanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('pc.aib.maintenance.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title"><i class="fas fa-tools me-2"></i>Registra Evento Manutenzione</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo di Risorsa</label>
                            <select name="asset_type" id="maintAssetType" class="form-select" required onchange="toggleAssetSelect()">
                                <option value="">Seleziona...</option>
                                <option value="App\Models\Vehicle">Veicolo / Mezzo d'Opera</option>
                                <option value="App\Models\MobileDevice">Dispositivo Mobile (Tablet / Smartphone)</option>
                                <option value="App\Models\CompanyPhone">Telefono / SIM (Generico)</option>
                            </select>
                        </div>

                        <!-- Select Vehicles -->
                        <div class="mb-3 d-none" id="wrap-vehicles">
                            <label class="form-label fw-bold">Seleziona Veicolo</label>
                            <select name="asset_id_vehicle" class="form-select asset-select vehicle-select">
                                <option value="">Scegli dal parco auto...</option>
                                @foreach(\App\Models\Vehicle::orderBy('targa')->get() as $v)
                                    <option value="{{ $v->id }}">{{ $v->targa }} - {{ $v->modello }} ({{ strtoupper($v->stato) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Select Devices -->
                        <div class="mb-3 d-none" id="wrap-devices">
                            <label class="form-label fw-bold">Seleziona Dispositivo</label>
                            <select name="asset_id_device" class="form-select asset-select device-select">
                                <option value="">Scegli il dispositivo...</option>
                                @foreach(\App\Models\MobileDevice::orderBy('modello')->get() as $d)
                                    <option value="{{ $d->id }}">{{ $d->modello }} - {{ $d->imei }} ({{ strtoupper($d->stato) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Select Phones -->
                        <div class="mb-3 d-none" id="wrap-phones">
                            <label class="form-label fw-bold">Seleziona Telefono/SIM</label>
                            <select name="asset_id_phone" class="form-select asset-select phone-select">
                                <option value="">Scegli la SIM/Telefono...</option>
                                @foreach(\App\Models\CompanyPhone::orderBy('numero')->get() as $p)
                                    <option value="{{ $p->id }}">{{ $p->numero }} ({{ strtoupper($p->stato) }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Hidden final asset_id -->
                        <input type="hidden" name="asset_id" id="final_asset_id" required>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo di Evento</label>
                            <select name="tipo_evento" class="form-select" required>
                                <option value="Inviato in Officina/Riparazione">Inviato in Officina/Riparazione</option>
                                <option value="Rientro da Manutenzione (Reintegro)">Rientro da Manutenzione (Reintegro)</option>
                                <option value="Preparazione Alienazione (Fermo)">Preparazione Alienazione (Fermo)</option>
                                <option value="Inviato a Demolizione (Definitivo)">Inviato a Demolizione (Definitivo)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Note Tecniche (Danni, Lavori Eseguiti, etc.)</label>
                            <textarea name="note_officina" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary fw-bold" onclick="setFinalAssetId()">Conferma e Registra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAssetSelect() {
            let type = document.getElementById('maintAssetType').value;
            document.getElementById('wrap-vehicles').classList.add('d-none');
            document.getElementById('wrap-devices').classList.add('d-none');
            document.getElementById('wrap-phones').classList.add('d-none');
            
            if (type === 'App\\Models\\Vehicle') {
                document.getElementById('wrap-vehicles').classList.remove('d-none');
            } else if (type === 'App\\Models\\MobileDevice') {
                document.getElementById('wrap-devices').classList.remove('d-none');
            } else if (type === 'App\\Models\\CompanyPhone') {
                document.getElementById('wrap-phones').classList.remove('d-none');
            }
        }
        
        function setFinalAssetId() {
            let type = document.getElementById('maintAssetType').value;
            let finalId = '';
            if (type === 'App\\Models\\Vehicle') {
                finalId = document.querySelector('.vehicle-select').value;
            } else if (type === 'App\\Models\\MobileDevice') {
                finalId = document.querySelector('.device-select').value;
            } else if (type === 'App\\Models\\CompanyPhone') {
                finalId = document.querySelector('.phone-select').value;
            }
            document.getElementById('final_asset_id').value = finalId;
        }
    </script>
</x-app-layout>
