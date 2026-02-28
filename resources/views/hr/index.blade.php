<x-app-layout>
    <x-slot name="header">
        Risorse Umane
    </x-slot>

    <style>
        .hr-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        .hr-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        }
        .hr-card .card-header-custom {
            padding: 20px 24px;
            color: white;
        }
        .hr-card .stat-number { font-size: 2em; font-weight: 700; }
        .hr-card .stat-label { font-size: 0.85em; opacity: 0.85; }
        .section-link { text-decoration: none; color: inherit; }
        .section-link:hover { color: inherit; }
        .placeholder-box {
            border: 2px dashed #ccc;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            color: #999;
            background: #fafafa;
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistiche rapide --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card hr-card shadow-sm h-100">
                <div class="card-header-custom" style="background: linear-gradient(135deg, #2e7d32, #43a047);">
                    <div class="stat-number">{{ number_format($stats['total_internal']) }}</div>
                    <div class="stat-label">Dipendenti Interni</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card hr-card shadow-sm h-100">
                <div class="card-header-custom" style="background: linear-gradient(135deg, #1565c0, #1e88e5);">
                    <div class="stat-number">{{ number_format($stats['active_internal']) }}</div>
                    <div class="stat-label">Attivi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card hr-card shadow-sm h-100">
                <div class="card-header-custom" style="background: linear-gradient(135deg, #e65100, #fb8c00);">
                    <div class="stat-number">{{ number_format($stats['aib_qualified']) }}</div>
                    <div class="stat-label">Qualificati AIB</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card hr-card shadow-sm h-100">
                <div class="card-header-custom" style="background: linear-gradient(135deg, #6a1b9a, #8e24aa);">
                    <div class="stat-number">{{ number_format($stats['total_external']) }}</div>
                    <div class="stat-label">Personale Esterno</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sezioni principali --}}
    <div class="row g-4">
        {{-- GESTIONE ANAGRAFICA --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('hr.internal.index') }}" class="section-link">
                <div class="card hr-card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 3em;" class="mb-3">📋</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Gestione Anagrafica</h5>
                        <p class="card-text text-muted small">Fascicolo personale, modifica dati, elenchi filtrati, aggiornamenti di gruppo del personale interno.</p>
                        <span class="badge bg-success">{{ number_format($stats['total_internal']) }} dipendenti</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- PERSONALE ESTERNO --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('hr.external.index') }}" class="section-link">
                <div class="card hr-card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 3em;" class="mb-3">🤝</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Personale Esterno</h5>
                        <p class="card-text text-muted small">Gestione collaboratori esterni, consulenti e personale in convenzione.</p>
                        <span class="badge bg-info">{{ number_format($stats['total_external']) }} collaboratori</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- CODICE FISCALE --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.tools.fiscal_code.index') }}" class="section-link">
                <div class="card hr-card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 3em;" class="mb-3">🔢</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Codice Fiscale</h5>
                        <p class="card-text text-muted small">Strumenti per il calcolo e la decodifica istantanea dei codici fiscali.</p>
                        <span class="badge bg-primary">Strumento Attivo</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- AREA AMMINISTRATIVA --}}
        <div class="col-md-6 col-lg-4">
            <div class="card hr-card shadow-sm h-100" style="opacity:0.7;">
                <div class="card-body text-center p-4">
                    <div style="font-size: 3em;" class="mb-3">🏢</div>
                    <h5 class="card-title fw-bold" style="color:#333;">Area Amministrativa</h5>
                    <p class="card-text text-muted small">Sezione riservata ai responsabili di area e agli utenti abilitati.</p>
                    <span class="badge bg-secondary">In sviluppo</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Spazio per future integrazioni --}}
    <div class="mt-4">
        <div class="placeholder-box">
            <div style="font-size: 1.5em; margin-bottom: 8px;">🔧</div>
            <p class="mb-0">Spazio riservato per future integrazioni e moduli aggiuntivi</p>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">← Torna alla Dashboard</a>
    </div>
</x-app-layout>
