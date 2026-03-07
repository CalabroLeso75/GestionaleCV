<x-app-layout>
    <x-slot name="header">
        Gestione Emergenze Protezione Civile
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row g-4 justify-content-center">
            <!-- Banner Informativo -->
            <div class="col-12">
                <div class="card shadow-sm border-0 bg-dark text-white overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary rounded-circle p-3 me-4 shadow">
                                <span style="font-size: 2em;">🚨</span>
                            </div>
                            <div>
                                <h3 class="mb-1">Pannello Operativo Emergenze</h3>
                                <p class="mb-0 opacity-75">Visualizzi le sotto-sezioni a cui sei autorizzato in base al tuo ruolo operativo.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sotto-sezioni abilitate -->
            @forelse($assignedAreas as $area)
            <div class="col-12 col-sm-6 col-lg-4 d-flex">
                <div class="card shadow-sm w-100 border-0 overflow-hidden area-card" 
                     {!! 'style="border-left: 4px solid ' . $area['color'] . ' !important;"' !!}>

                    <a href="{{ $area['url'] }}" class="card-body p-4 text-center d-flex flex-column text-decoration-none">
                        <div class="mb-3" style="font-size: 2.5em;">{{ $area['icon'] }}</div>
                        <h5 class="card-title fw-bold" style="color:#2c3e50;">{{ $area['name'] }}</h5>
                        <p class="card-text text-muted small mb-3">{{ $area['description'] ?? 'Sezione dedicata alle operazioni di '.$area['name'] }}</p>

                        <div class="mt-auto pt-3 border-top">
                            <div class="d-flex flex-column gap-2">
                                <!-- Role Badge -->
                                <div>
                                    <span class="badge rounded-pill px-3 py-1" 
                                          {!! 'style="background-color: ' . $area['color'] . '15; color: ' . $area['color'] . '; border: 1px solid ' . $area['color'] . '30; font-size: 0.8em;"' !!}>
                                        <i class="fas fa-user-shield me-1"></i> {{ $area['role'] }}
                                    </span>
                                </div>

                                <!-- Privilege Level -->
                                @php
                                    $levelColors = [
                                        1 => ['bg' => '#198754', 'text' => 'white'],
                                        2 => ['bg' => '#20c997', 'text' => 'white'],
                                        3 => ['bg' => '#ffc107', 'text' => 'black'],
                                        4 => ['bg' => '#fd7e14', 'text' => 'white'],
                                        5 => ['bg' => '#dc3545', 'text' => 'white'],
                                    ];
                                    $lv = $area['privilege_level'];
                                    $c = $levelColors[$lv] ?? ['bg' => '#6c757d', 'text' => 'white'];
                                @endphp
                                
                                <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                    <span class="badge rounded-pill px-3 py-1 shadow-sm" 
                                          {!! 'style="background-color: ' . $c['bg'] . '; color: ' . $c['text'] . '; font-size: 0.75em; min-width: 140px;"' !!}>
                                        Livello {{ $lv }}: {{ $area['privilege_label'] }}
                                    </span>
                                    
                                    @if(isset($area['provinces']))
                                        <div class="w-100 mt-2">
                                            <small class="text-muted d-block mb-1">Province Abilitate:</small>
                                            <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                @foreach($area['provinces'] as $prov)
                                                    <span class="badge bg-light text-dark border" style="font-size: 0.65em;">
                                                        {{ explode(' di ', $prov['name'])[1] ?? $prov['name'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-1" style="font-size: 0.7em; opacity: 0.7;">
                                            <i class="fas fa-wrench me-1"></i> Prossimamente
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="bg-light p-5 rounded shadow-sm d-inline-block">
                    <div style="font-size: 4em;" class="mb-3">🚫</div>
                    <h4 class="text-muted">Nessuna abilitazione trovata</h4>
                    <p>Non risulti assegnato ad alcuna sotto-sezione operativa.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    @push('styles')
    <style>
        .area-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-radius: 12px;
            min-height: 320px;
        }
        .area-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        }
        .area-card a {
            color: inherit;
        }
        .badge { font-weight: 500; }
    </style>
    @endpush
</x-app-layout>
