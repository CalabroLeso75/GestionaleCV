<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark">
            <i class="fas fa-history text-primary me-2"></i> {{ __('Registro Storico Assegnazioni') }}
        </h2>
        <p class="text-muted mb-0">Tracciamento consegne, cambi e ritiri di veicoli e dispositivi alle squadre</p>
    </x-slot>

    <div class="py-12">
        <div class="container-fluid">
            
            <!-- Filtri -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('pc.aib.asset_logs.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Sigla Squadra</label>
                            <input type="text" name="squadra" class="form-control" placeholder="Es. CZRE..." value="{{ request('squadra') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tipo Risorsa</label>
                            <select name="asset_type" class="form-select">
                                <option value="">Tutti</option>
                                <option value="Veicolo" {{ request('asset_type') == 'Veicolo' ? 'selected' : '' }}>Veicoli</option>
                                <option value="Telefono con SIM" {{ request('asset_type') == 'Telefono con SIM' ? 'selected' : '' }}>Telefoni con SIM</option>
                                <option value="Dispositivo Mobile" {{ request('asset_type') == 'Dispositivo Mobile' ? 'selected' : '' }}>Dispositivi Mobili</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filtra
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('pc.aib.asset_logs.index') }}" class="btn btn-light w-100">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabella -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Data e Ora</th>
                                    <th>Azione</th>
                                    <th>Risorsa Assegnata</th>
                                    <th>Squadra</th>
                                    <th>Precedenti Capisquadra</th>
                                    <th>Nuovi Capisquadra</th>
                                    <th>Operatore (Sistema)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if($log->action == 'Consegna Iniziale')
                                                <span class="badge bg-success"><i class="fas fa-truck-loading me-1"></i> Consegna</span>
                                            @elseif($log->action == 'Cambio Assegnatari')
                                                <span class="badge bg-warning text-dark"><i class="fas fa-exchange-alt me-1"></i> Cambio Assegnatario</span>
                                            @else
                                                <span class="badge bg-danger"><i class="fas fa-box-open me-1"></i> Ritiro</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $log->asset_name }}</strong><br>
                                            <small class="text-muted">{{ $log->asset_type }}</small>
                                        </td>
                                        <td>
                                            @if($log->team)
                                                <span class="badge bg-primary">{{ $log->team->sigla }}</span>
                                            @else
                                                <span class="text-muted">Squadra Rimossa</span>
                                            @endif
                                        </td>
                                        <td class="text-muted text-decoration-line-through">
                                            @if($log->old_assignees && count($log->old_assignees) > 0)
                                                {{ implode(', ', $log->old_assignees) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="fw-bold text-primary">
                                            @if($log->new_assignees && count($log->new_assignees) > 0)
                                                {{ implode(', ', $log->new_assignees) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-user-circle text-muted me-1"></i> {{ $log->user ? $log->user->name : 'Sistema' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="fas fa-clipboard-list fa-3x mb-3 text-light"></i><br>
                                            Nessun movimento registrato.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
