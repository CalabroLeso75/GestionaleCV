<x-app-layout>
    <x-slot name="header">
        Amministrazione di Sistema
    </x-slot>

    <style>
        .admin-card { transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
        .admin-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
    </style>

    <p class="text-muted mb-4">Pannello di controllo riservato agli amministratori di sistema.</p>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.site.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #1565c0 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">⚙️</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Impostazioni Sito</h5>
                        <p class="card-text text-muted small">URL, nome applicazione, ambiente di esecuzione</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.smtp.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #2e7d32 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📧</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Configurazione Email</h5>
                        <p class="card-text text-muted small">Server SMTP, credenziali, test invio</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.emails.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #d81b60 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📞</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Rubrica Email</h5>
                        <p class="card-text text-muted small">Gestisci i destinatari SOUP, COP e Test</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #e65100 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">👥</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Gestione Utenti</h5>
                        <p class="card-text text-muted small">Approvazione, ruoli, utenti registrati</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.style.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #6a1b9a !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🎨</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Stile e Template</h5>
                        <p class="card-text text-muted small">Personalizzazione grafica del sito</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.sections.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #00838f !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🧩</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Gestione Sezioni</h5>
                        <p class="card-text text-muted small">Crea e gestisci le tessere della dashboard</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.profiles.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #f57c00 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">👮</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Gestione Profili</h5>
                        <p class="card-text text-muted small">Pacchetti sezioni per ruolo — chi vede cosa</p>
                    </div>
                </div>
            </a>
        </div>

        @if(Auth::user() && Auth::user()->hasRole('super-admin'))
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.filemanager.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #00acc1 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📂</div>
                        <h5 class="card-title fw-bold" style="color:#333;">File Manager</h5>
                        <p class="card-text text-muted small">Gestisci i file sul server (Super Admin)</p>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.dbmanager.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 admin-card" style="border-left: 4px solid #ffb300 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🗄️</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Database Manager</h5>
                        <p class="card-text text-muted small">Esplora e gestisci il DB SQL (Super Admin)</p>
                    </div>
                </div>
            </a>
        </div>
        @endif
    </div>

    <x-back-button />

</x-app-layout>
