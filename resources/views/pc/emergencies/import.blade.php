<x-app-layout>
    <x-slot name="header">
        Importazione Dati da Sistema PC2
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-file-csv fa-3x"></i>
                        </div>
                        <h4 class="fw-bold">Importatore Eventi PC2</h4>
                        <p class="text-muted">Carica il file CSV esportato dal sistema PC2 per popolare istantaneamente il database delle emergenze.</p>
                    </div>

                    <form action="{{ route('pc.emergencies.import.post') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">Seleziona File CSV</label>
                            <div class="input-group">
                                <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                            </div>
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i> Il file deve utilizzare il punto e virgola (;) come separatore.
                            </div>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3">Mapping Colonne Atteso:</h6>
                                <ul class="small text-muted mb-0">
                                    <li><code>ID_PCM</code>: Identificativo unico PC2</li>
                                    <li><code>Data</code>: Data e ora dell'evento</li>
                                    <li><code>Comune</code>: Nome del comune (es. Catanzaro)</li>
                                    <li><code>Indirizzo</code>: Località o via</li>
                                    <li><code>Tipo</code>: Tipologia di emergenza</li>
                                    <li><code>Priorita</code>: Bassa, Media, Alta, Critica</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg py-3">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Avvia Elaborazione e Importa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted small">
                <a href="{{ route('pc.emergencies.index') }}" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Torna al Monitoraggio
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
