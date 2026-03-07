<x-app-layout>
    <x-slot name="header">
        Dashboard
        <style>
            .dashboard-card {
                transition: transform 0.2s, box-shadow 0.2s;
                border-radius: 12px;
            }
            .dashboard-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            }
        </style>
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

        {{-- Sezioni dinamiche L1 dalla tabella dashboard_sections --}}
        @php
            $l1Sections = \App\Models\DashboardSection::rootsVisibleTo(Auth::user());
            // Fetch ultimi 3 report se utente ha ruoli collegati a operazioni
            $recentReports = [];
            if(Auth::user()->hasRole('dos') || Auth::user()->hasRole('operatore') || Auth::user()->hasRole('super-admin')) {
                $recentReports = \App\Models\EmergencyReport::with('user')->orderBy('created_at', 'desc')->take(3)->get();
            }
        @endphp

        @if(count($recentReports) > 0)
        <!-- Tile Ultime Rilevazioni DOS -->
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('dos.history') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 dashboard-card p-0" style="border-left: 4px solid #dc3545 !important; overflow: hidden;">
                    <div class="card-header bg-white border-0 pt-3 pb-0 text-center">
                        <div style="font-size: 1.8em;" class="mb-1 text-danger">🚨</div>
                        <h5 class="card-title mb-0" style="color:#333;">Ultime Rilevazioni</h5>
                    </div>
                    <div class="card-body p-2">
                        <ul class="list-group list-group-flush text-start small">
                            @foreach($recentReports as $rep)
                                @php
                                    $dLat = floor($rep->fire_lat);
                                    $mLat = floor(($rep->fire_lat - $dLat) * 60);
                                    $dLng = floor($rep->fire_lng);
                                    $mLng = floor(($rep->fire_lng - $dLng) * 60);
                                    $dmsStr = "{$dLat}°{$mLat}'N {$dLng}°{$mLng}'E";
                                @endphp
                                <li class="list-group-item px-2 py-1 border-bottom-0" style="font-size: 0.8em; line-height: 1.2;">
                                    <div class="fw-bold text-muted">{{ $rep->created_at->format('d/m H:i') }} - {{ $rep->user->name ?? 'N/D' }} ({{ $rep->role_snapshot }})</div>
                                    <div class="text-dark fw-semibold text-truncate">{{ $rep->municipality ?: 'N/D' }} {{ $rep->province ? '('.$rep->province.')' : '' }}</div>
                                    <div class="text-muted"><i class="fw-bold">{{ $dmsStr }}</i></div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </a>
        </div>
        @endif

        @foreach($l1Sections as $section)
        <div class="col-md-6 col-lg-4">
            @if($section->route)
            <a href="{{ \Illuminate\Support\Facades\Route::has($section->route) ? route($section->route) : url($section->route) }}" class="text-decoration-none">
            @else
            <div>
            @endif
                <div class="card shadow-sm h-100 border-0 dashboard-card" {!! 'style="border-left: 4px solid ' . $section->color . ' !important;"' !!}>
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

    {{-- ─── ACCESSI RAPIDI (L2/L3/L4) ────────────────────────────────── --}}
    @php $deepLinks = \App\Models\DashboardSection::deepLinksVisibleTo(Auth::user()); @endphp
    @if($deepLinks->isNotEmpty())
    <div class="mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex align-items-center justify-content-between py-2 bg-white" style="cursor:pointer;" onclick="this.nextElementSibling.classList.toggle('d-none'); this.querySelector('.ql-icon').textContent = this.nextElementSibling.classList.contains('d-none') ? '▶' : '▼'">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-5">⚡</span>
                    <strong>Accessi Rapidi</strong>
                    <span class="badge bg-secondary small">{{ $deepLinks->sum(fn($g) => $g->count()) }} sezioni</span>
                </div>
                <span class="ql-icon text-muted">▼</span>
            </div>
            <div class="card-body p-3">
                @foreach($deepLinks as $parentTitle => $group)
                <div class="mb-3">
                    <div class="text-muted fw-semibold mb-2 small text-uppercase" style="letter-spacing:0.05em; border-bottom:1px solid #dee2e6; padding-bottom:4px;">{{ $parentTitle }}</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($group as $link)
                        @php
                            $href = null;
                            if ($link->route) {
                                try {
                                    $href = \Illuminate\Support\Facades\Route::has($link->route) ? route($link->route) : url($link->route);
                                } catch(\Exception $e) { $href = url($link->route); }
                            }
                        @endphp
                        <{{ $href ? 'a href="'.$href.'"' : 'span' }} class="text-decoration-none">
                            <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border"
                                 style="background:#f8f9fa; border-left:3px solid {{ $link->color }} !important; transition:background 0.15s;"
                                 onmouseover="this.style.background='#e9ecef'" onmouseout="this.style.background='#f8f9fa'">
                                <span>{{ $link->icon }}</span>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.85em; color:#333;">{{ $link->title }}</div>
                                    @if($link->description)
                                        <div class="text-muted" style="font-size:0.72em;">{{ $link->description }}</div>
                                    @endif
                                </div>
                                <span class="badge ms-1" style="font-size:0.6em; background:{{ $link->color }}; color:#fff;">L{{ $link->level }}</span>
                            </div>
                        </{{ $href ? 'a' : 'span' }}>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <x-back-button />

</x-app-layout>

