<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard dell\'Architetto (IA Locale)') }}
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- AI Stats & Hero -->
            <div class="bg-dark text-white rounded-3xl p-8 mb-8 shadow-2xl position-relative overflow-hidden" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
                <div class="position-absolute top-0 end-0 p-5 opacity-10">
                    <i class="fas fa-brain fa-10x"></i>
                </div>
                
                <div class="row align-items-center position-relative">
                    <div class="col-lg-8">
                        <span class="badge bg-primary mb-3 px-3 py-2 text-uppercase tracking-wider">Esperimento Smart Sviluppo</span>
                        <h1 class="display-4 fw-bold mb-3">Benvenuto, Architetto.</h1>
                        <p class="lead mb-4 opacity-75">Sfrutta la potenza di <strong>Llama 3</strong> locale per monitorare l'integrità del sistema, ottimizzare il database e anticipare i colli di bottiglia durante lo sviluppo.</p>
                        
                        <div class="d-flex gap-3">
                            <button onclick="runAudit('db_performance')" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm border-0" style="background: #e94560;">
                                <i class="fas fa-database me-2"></i> Audit Database
                            </button>
                            <button onclick="runAudit('code_quality')" class="btn btn-outline-light btn-lg rounded-pill px-4">
                                <i class="fas fa-shield-alt me-2"></i> Audit Sicurezza
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Terminal Output -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-3xl overflow-hidden mb-4">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-terminal me-2 text-primary"></i> Report di Sistema</h5>
                            <div id="status-dots" class="d-flex gap-1">
                                <span class="dot bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                                <span class="dot bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                                <span class="dot bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                            </div>
                        </div>
                        <div class="card-body bg-light p-0 position-relative">
                            <div id="audit-loading" class="position-absolute top-50 start-50 translate-middle d-none text-center">
                                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                                <p class="text-muted fw-bold">L'IA sta elaborando i dati del tuo PC...</p>
                            </div>
                            <div id="audit-placeholder" class="p-5 text-center text-muted">
                                <i class="far fa-clipboard fa-4x mb-3 opacity-25"></i>
                                <p>Seleziona un'azione per iniziare l'analisi.</p>
                            </div>
                            <pre id="audit-content" class="p-4 m-0 text-dark font-monospace d-none" style="white-space: pre-wrap; font-size: 0.9rem; max-height: 600px; overflow-y: auto;"></pre>
                        </div>
                    </div>
                </div>

                <!-- Side Cards -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3xl mb-4 bg-info text-white">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3"><i class="fas fa-microchip fa-3x"></i></div>
                            <h6 class="text-uppercase mb-2 opacity-75">Potenza Locale</h6>
                            <h3 class="fw-bold">PC PC-Lele2022</h3>
                            <p class="small mb-0 opacity-75">64GB RAM | i7-12700H</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-3xl">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3">Stato Ollama</h6>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success rounded-circle me-3" style="width: 12px; height: 12px;"></div>
                                <span class="fw-bold">Online & Pronto</span>
                            </div>
                            <div class="p-3 bg-light rounded-3 border border-dashed text-center">
                                <span class="small text-muted">Modello Attivo:</span><br>
                                <span class="badge bg-dark">Llama 3 (8B)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function runAudit(type) {
        const placeholder = document.getElementById('audit-placeholder');
        const loading = document.getElementById('audit-loading');
        const content = document.getElementById('audit-content');
        
        placeholder.classList.add('d-none');
        loading.classList.remove('d-none');
        content.classList.add('d-none');
        content.textContent = "";

        fetch('{{ route("admin.ai.audit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type: type })
        })
        .then(response => response.json())
        .then(data => {
            loading.classList.add('d-none');
            if (data.report) {
                content.classList.remove('d-none');
                content.textContent = data.report;
            } else {
                placeholder.classList.remove('d-none');
                alert('Errore: ' + (data.error || 'Risposta vuota'));
            }
        })
        .catch(error => {
            loading.classList.add('d-none');
            placeholder.classList.remove('d-none');
            alert('Errore connessione: ' + error);
        });
    }
    </script>

    <style>
    .rounded-3xl { border-radius: 1.5rem !important; }
    .tracking-wider { letter-spacing: 0.1em; }
    pre::-webkit-scrollbar { width: 8px; }
    pre::-webkit-scrollbar-track { background: #f1f1f1; }
    pre::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
    pre::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</x-app-layout>
