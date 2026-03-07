<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h2 class="h4 font-weight-bold text-dark mb-0">Gestione Emergenze PC - Real-time Dashboard</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('pc.emergencies.import') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-import me-1"></i>Importa PC2
                </a>
                <button class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Nuovo Evento
                </button>
            </div>
        </div>
    </x-slot>

    <div class="row g-4 mb-4">
        <!-- Stats Cards -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-danger text-white overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="small fw-bold opacity-75">EVENTI CRITICI</div>
                    <div class="display-6 fw-bold">{{ $incidents->where('priorita', 'Critica')->count() }}</div>
                    <i class="fas fa-exclamation-triangle position-absolute end-0 bottom-0 mb-3 me-3 opacity-25" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="small fw-bold opacity-75">IN GESTIONE</div>
                    <div class="display-6 fw-bold">{{ $incidents->where('stato', 'In Gestione')->count() }}</div>
                    <i class="fas fa-clock position-absolute end-0 bottom-0 mb-3 me-3 opacity-25" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-light border-0 shadow-sm d-flex align-items-center mb-0 h-100 p-4">
                <div class="spinner-grow text-danger spinner-grow-sm me-3" role="status"></div>
                <div class="flex-grow-1">
                    <div class="small fw-bold text-muted">STATO SINTESI</div>
                    <div class="text-dark small">Monitoraggio attivo su tutto il territorio regionale. Ultima scansione: {{ now()->format('H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Incidents Cards -->
    <div class="row g-4" id="incidentContainer">
        @forelse($incidents as $incident)
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm incident-card overflow-hidden" data-id="{{ $incident->id }}">
                <div class="card-header border-0 bg-transparent py-3 px-4 d-flex justify-content-between align-items-center">
                    @php $badgeClass = 'priority-badge-' . strtolower(str_replace(' ', '-', $incident->priorita)); @endphp
                    <span class="badge rounded-pill {{ $badgeClass }}">
                        {{ $incident->priorita }}
                    </span>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($incident->data_ora)->diffForHumans() }}</small>
                </div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-light rounded p-3 me-3 flex-shrink-0 text-center" style="width: 50px;">
                            <span style="font-size: 1.2rem;">{{ $incident->tipo_evento === 'Incendio' ? '🔥' : '⚠️' }}</span>
                        </div>
                        <div>
                            <h5 class="card-title fw-bold mb-1">{{ $incident->tipo_evento }}</h5>
                            <div class="text-primary small fw-bold">
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $incident->comune->name }} ({{ $incident->comune->province->abbreviation ?? 'CZ' }})
                            </div>
                        </div>
                    </div>
                    
                    <p class="card-text text-muted small mb-4 line-clamp-2">
                        {{ $incident->descrizione ?? 'Nessuna descrizione aggiuntiva fornita per questo evento.' }}
                    </p>

                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <div class="small">
                            <span class="text-muted">Codice:</span> <code class="bg-light px-2 rounded">{{ $incident->codice_incidente }}</code>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary rounded-circle" title="Vedi Dettagli">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="progress" style="height: 3px;">
                    <div class="progress-bar progress-bar-animated progress-bar-striped {{ $incident->priorita === 'Critica' ? 'bg-danger' : 'bg-primary' }}" 
                         role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="bg-light p-5 rounded-4 shadow-sm d-inline-block">
                <div class="display-1 mb-4">✅</div>
                <h3 class="text-dark fw-bold">Nessuna emergenza attiva</h3>
                <p class="text-muted">Tutto tranquillo. Non ci sono incidenti che richiedono attenzione immediata.</p>
                <a href="{{ route('pc.emergencies.import') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-file-import me-2"></i>Carica dati PC2
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @push('styles')
    <style>
        .incident-card {
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            border-bottom: 3px solid transparent;
        }
        .incident-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
            border-bottom: 3px solid #d32f2f;
        }
        .priority-badge-critica { background-color: #fce4e4; color: #d32f2f; }
        .priority-badge-molto-alta { background-color: #fff3e0; color: #ef6c00; }
        .priority-badge-alta { background-color: #fffde7; color: #fbc02d; }
        .priority-badge-media { background-color: #e3f2fd; color: #1976d2; }
        .priority-badge-bassa { background-color: #e8f5e9; color: #388e3c; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
    @endpush
</x-app-layout>
