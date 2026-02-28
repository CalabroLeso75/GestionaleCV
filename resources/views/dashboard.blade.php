<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="row g-4">
        <!-- Tessera Benvenuto -->
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #0066cc 0%, #004999 100%); color: white;">
                <div class="card-body p-4">
                    <h3 class="mb-1">Benvenuto, {{ Auth::user()->name }} {{ Auth::user()->surname }}</h3>
                    <p class="mb-0 opacity-75">Sei connesso al Gestionale Calabria Verde.</p>
                </div>
            </div>
        </div>

        <!-- Avvisi Scadenze Autoparco -->
        @if($expiringAssicurazione > 0 || $expiringRevisione > 0)
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-danger bg-opacity-10 border-start border-danger border-4">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3 fs-3">🚨</div>
                        <div>
                            <h6 class="mb-0 fw-bold text-danger">Scadenze Autoparco</h6>
                            <p class="mb-0 small text-muted">
                                Ci sono <b>{{ $expiringAssicurazione + $expiringRevisione }}</b> scadenze imminenti (Assicurazioni/Revisioni) nei prossimi 30 giorni.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('autoparco.index') }}" class="btn btn-sm btn-danger shadow-sm">Verifica Mezzi</a>
                </div>
            </div>
        </div>
        @endif

        <!-- Tessera Admin (solo per super-admin) -->
        @if(Auth::user()->hasRole('super-admin') || Auth::user()->email === 'raffaele.cusano@calabriaverde.eu')
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 4px solid #d32f2f !important;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🛡️</div>
                        <h5 class="card-title" style="color:#333;">Amministrazione di Sistema</h5>
                        <p class="card-text text-muted small">Impostazioni, utenti, sezioni, stile</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tessera Log Attività -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.logs.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 4px solid #455a64 !important;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📜</div>
                        <h5 class="card-title" style="color:#333;">Log Attività</h5>
                        <p class="card-text text-muted small">Registrazione eventi, login, modifiche</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- AI Architect Card -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.ai.architect') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 4px solid #6f42c1 !important;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🧠</div>
                        <h5 class="card-title" style="color:#333;">IA Architetto</h5>
                        <p class="card-text text-muted small">Sfrutta l'IA locale per ottimizzare il sistema e l'architettura.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Fiscal Code Tools Card -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.tools.fiscal_code.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 4px solid #198754 !important;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🆔</div>
                        <h5 class="card-title" style="color:#333;">Strumenti CF</h5>
                        <p class="card-text text-muted small">Calcola il Codice Fiscale o estrai dati da uno esistente.</p>
                    </div>
                </div>
            </a>
        </div>

        @endif

        <!-- Sezioni dinamiche dalla tabella dashboard_sections -->
        @php
            $sections = \App\Models\DashboardSection::visibleTo(Auth::user());
        @endphp

        @foreach($sections as $section)
        <div class="col-md-6 col-lg-4">
            @if($section->route)
            <a href="{{ url($section->route) }}" class="text-decoration-none">
            @else
            <div>
            @endif
                <div class="card shadow-sm h-100 border-0" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 4px solid {{ $section->color }} !important;"
                     onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,0.15)'"
                     onmouseout="this.style.transform=''; this.style.boxShadow=''">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">{{ $section->icon }}</div>
                        <h5 class="card-title" style="color:#333;">{{ $section->title }}</h5>
                        @if($section->description)
                            <p class="card-text text-muted small">{{ $section->description }}</p>
                        @endif
                    </div>
                </div>
            @if($section->route)
            </a>
            @else
            </div>
            @endif
        </div>
        @endforeach
    </div>
</x-app-layout>
