<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">📦 Magazzino Regionale</h2>
        </div>
        <style>
            .wh-card { transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; }
            .wh-card:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        </style>
    </x-slot>

    <div class="row g-4">

        {{-- KPI Bar - stile Risorse Umane --}}
        <div class="col-12">
            <div class="row g-3">
                <x-kpi-card color="green"  label="Prodotti a Catalogo" :value="\App\Models\WarehouseProduct::count()" />
                <x-kpi-card color="blue"   label="Ubicazioni"          :value="\App\Models\WarehouseLocation::count()" />
                <x-kpi-card color="orange" label="Movimenti Oggi"      :value="\App\Models\WarehouseMovement::whereDate('created_at', today())->count()" />
                <x-kpi-card color="purple" label="Sotto Scorta Min."   :value="\App\Models\WarehouseStock::whereColumn('quantity', '<', 'min_stock')->count()" />
            </div>
        </div>

        {{-- MODULE TILES --}}

        {{-- Catalogo Prodotti --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('magazzino.prodotti.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 wh-card" style="border-left: 4px solid #2e7d32 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📋</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Catalogo Prodotti</h5>
                        <p class="card-text text-muted small">Articoli, barcode, categorie e unità di misura. Scansiona per cercare o creare.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Albero Ubicazioni --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('magazzino.locations.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 wh-card" style="border-left: 4px solid #1565c0 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🗺️</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Ubicazioni &amp; Distretti</h5>
                        <p class="card-text text-muted small">Sede Centrale, 11 Distretti, HUB, Magazzini, Distaccamenti e Punti di Consumo.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Giacenze --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('magazzino.stock.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 wh-card" style="border-left: 4px solid #e65100 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📊</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Giacenze &amp; Scorte</h5>
                        <p class="card-text text-muted small">Quantità disponibili per ubicazione, scorte minime e ottimali.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Movimenti --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('magazzino.movimenti.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 wh-card" style="border-left: 4px solid #6a1b9a !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">🔁</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Movimenti</h5>
                        <p class="card-text text-muted small">Carico, scarico, trasferimenti, smistamenti e assegnazioni.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- SIM Telefoniche --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('pc.aib.phones.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 wh-card" style="border-left: 4px solid #0288d1 !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📶</div>
                        <h5 class="card-title fw-bold" style="color:#333;">SIM Telefoniche</h5>
                        <p class="card-text text-muted small">Gestione schede SIM, assegnazioni e stato attivo/disattivo.</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Dispositivi Mobili --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('pc.aib.mobile_devices.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 border-0 wh-card" style="border-left: 4px solid #00838f !important;">
                    <div class="card-body text-center p-4">
                        <div style="font-size: 2.5em;" class="mb-2">📱</div>
                        <h5 class="card-title fw-bold" style="color:#333;">Dispositivi Mobili</h5>
                        <p class="card-text text-muted small">Smartphone, tablet, IMEI, specifiche tecniche e assegnazioni.</p>
                    </div>
                </div>
            </a>
        </div>

    </div>

    <x-back-button :url="route('dashboard')" label="← Torna alla Dashboard" />

</x-app-layout>
