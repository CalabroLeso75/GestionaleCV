<x-app-layout>
    <x-slot name="header">
        Amministrazione di Sistema
    </x-slot>

    <p class="text-muted mb-4">Pannello di controllo riservato agli amministratori di sistema.</p>

    <div class="row g-4">
        <!-- Impostazioni Sito -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.site.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s;" 
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">⚙️</div>
                        <h5 class="card-title" style="color:#333;">Impostazioni Sito</h5>
                        <p class="card-text text-muted small">URL, nome applicazione, ambiente di esecuzione</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- SMTP Email -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.smtp.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📧</div>
                        <h5 class="card-title" style="color:#333;">Configurazione Email</h5>
                        <p class="card-text text-muted small">Server SMTP, credenziali, test invio</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gestione Utenti -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">👥</div>
                        <h5 class="card-title" style="color:#333;">Gestione Utenti</h5>
                        <p class="card-text text-muted small">Approvazione, ruoli, utenti registrati</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Stile e Template -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.style.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🎨</div>
                        <h5 class="card-title" style="color:#333;">Stile e Template</h5>
                        <p class="card-text text-muted small">Personalizzazione grafica del sito</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Gestione Sezioni -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.sections.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🧩</div>
                        <h5 class="card-title" style="color:#333;">Gestione Sezioni</h5>
                        <p class="card-text text-muted small">Crea e gestisci le tessere della dashboard</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
