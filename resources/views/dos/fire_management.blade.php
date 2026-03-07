<x-app-layout>

    <x-slot name="header">
        Gestione Incendio
    </x-slot>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<!-- Leaflet Draw CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
<!-- FontAwesome for markers -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
    /* Styling Mobile-First Premium */
    .dos-app-container {
        max-width: 600px;
        margin: 0 auto;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .dos-header {
        background: linear-gradient(135deg, #e74a3b 0%, #c82333 100%);
        color: white;
        padding: 20px;
        text-align: center;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        margin-bottom: 20px;
    }

    .dos-label {
        font-weight: 600;
        color: #5a5c69;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .form-control[readonly] {
        background-color: #f8f9fc;
        border: 1px solid #eaecf4;
        font-family: monospace;
        font-size: 1rem;
        color: #3a3b45;
    }

    .btn-map-action {
        background: #4e73df;
        color: white;
        border-radius: 50px;
        padding: 12px 25px;
        font-size: 1.1rem;
        font-weight: bold;
        width: 100%;
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        transition: all 0.3s ease;
    }
    .btn-map-action:hover, .btn-map-action:active {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(78, 115, 223, 0.6);
        color: white;
    }

    .btn-send-report {
        background: #1cc88a;
        color: white;
        border-radius: 50px;
        padding: 15px 25px;
        font-size: 1.2rem;
        font-weight: bold;
        width: 100%;
        box-shadow: 0 5px 15px rgba(28, 200, 138, 0.4);
        margin-top: 20px;
        transition: all 0.3s ease;
    }
    .btn-send-report:hover, .btn-send-report:active {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(28, 200, 138, 0.6);
        color: white;
    }

    /* Fullscreen Map Overlay */
    #mapOverlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        background: #000;
        display: none;
    }
    #mapContainer {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .map-ui-top {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        background: rgba(255,255,255,0.95);
        padding: 10px 20px;
        border-radius: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        gap: 15px;
        width: 90%;
        max-width: 400px;
    }

    .map-ui-bottom {
        position: absolute;
        bottom: 80px; /* Increased to avoid mobile bottom bars */
        left: 50%;
        transform: translateX(-50%);
        z-index: 10000;
        width: 90%;
        max-width: 400px;
        display: flex;
        gap: 10px;
    }

    .stat-box {
        text-align: center;
        flex: 1;
    }
    .stat-val {
        font-size: 1.2rem;
        font-weight: bold;
        color: #e74a3b;
        font-family: monospace;
    }
    .stat-lbl {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #858796;
        font-weight: bold;
    }

    .marker-compass {
        transition: transform 0.1s ease-out;
    }
</style>

<div class="container-fluid py-4">
    <div class="dos-app-container">

        <div class="dos-header">
            <h3 class="font-weight-bold mb-1"><i class="fas fa-tree text-success"></i> <i class="fas fa-fire text-warning"></i> Gestione Incendio</h3>
            <p class="mb-0 small text-white-50">
                Pannello <strong>{{ auth()->user()->hasRole('dos') ? 'D.O.S.' : 'Operatore' }}</strong>: {{ auth()->user()->name }} {{ auth()->user()->surname }}
            </p>
        </div>

        <div class="px-4 pb-4">

            <button type="button" class="btn btn-map-action mb-4" onclick="openFullscreenMap()">
                <i class="fas fa-satellite-dish mr-2"></i> Acquisizione Dati su Mappa
            </button>

            <form id="dosReportForm">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="dos-label">Mia Latitudine</label>
                            <input type="text" class="form-control text-center" id="f_op_lat" name="op_lat" readonly placeholder="---">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="dos-label">Mia Longitudine</label>
                            <input type="text" class="form-control text-center" id="f_op_lng" name="op_lng" readonly placeholder="---">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="dos-label text-danger">Lat. Incendio</label>
                            <input type="text" class="form-control text-center border-left-danger" id="f_fire_lat" name="fire_lat" readonly placeholder="---">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="dos-label text-danger">Lng. Incendio</label>
                            <input type="text" class="form-control text-center border-left-danger" id="f_fire_lng" name="fire_lng" readonly placeholder="---">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-8">
                        <div class="mb-3">
                            <label class="dos-label text-info">Comune</label>
                            <select class="form-control border-left-info" id="f_municipality" name="municipality_id">
                                <option value="">--- Seleziona prima Provincia ---</option>
                            </select>
                            <input type="hidden" id="f_municipality_name" name="municipality">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-3">
                            <label class="dos-label text-info">Prov</label>
                            <select class="form-control border-left-info" id="f_province" name="province_id">
                                <option value="">---</option>
                            </select>
                            <input type="hidden" id="f_province_name" name="province">
                        </div>
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="dos-label text-secondary">Toponimi / Località Vicine</label>
                            <select class="form-control" id="f_toponyms_display" name="nearest_toponyms">
                                <option value="">Seleziona la mappa...</option>
                            </select>
                            <input type="hidden" name="nearest_toponyms_json" id="f_nearest_toponyms_json">
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row mt-2">
                    <div class="col-12 text-center" id="f_ephemeris">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <div class="row text-center mb-3 mt-3">
                    <div class="col-4 px-1">
                        <label class="d-block mb-1" style="font-size: 0.70rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Distanza</label>
                        <input type="text" class="form-control text-center font-weight-bold text-primary px-1" id="f_distance" name="distance" readonly placeholder="0">
                    </div>
                    <div class="col-4 px-1">
                        <label class="d-block mb-1" style="font-size: 0.70rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Temp (°C)</label>
                        <input type="text" class="form-control text-center font-weight-bold text-warning px-1" id="f_temperature" name="temperature" readonly placeholder="0">
                    </div>
                    <div class="col-4 px-1">
                        <label class="d-block mb-1" style="font-size: 0.70rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Vento</label>
                        <input type="text" class="form-control text-center font-weight-bold text-info px-1" id="f_wind_speed" name="wind_speed" readonly placeholder="0">
                        <!-- Hidden input for direction -->
                        <input type="hidden" id="f_wind_dir" name="wind_direction">
                    </div>
                </div>

                <h6 class="text-center font-weight-bold mt-4 mb-2 text-muted" style="font-size: 0.8rem;">PREVISIONI VENTO ORARIE</h6>
                <div class="row mb-3 pb-2 border-bottom" id="wind_forecasts_container">
                    <!-- Filled by JS -->
                    <div class="col-12 text-center small text-muted">Caricamento meteo...</div>
                </div>

                <!-- Dati GIS -->
                <input type="hidden" name="polygon_geojson" id="f_polygon_geojson">
                <input type="hidden" name="area_hectares" id="f_area_hectares">
                <input type="hidden" name="front_meters" id="f_front_meters">

                <!-- Hidden inputs per Previsioni Vento -->
                <input type="hidden" name="wind_forecast_2h_speed" id="f_w2s">
                <input type="hidden" name="wind_forecast_2h_dir" id="f_w2d">
                <input type="hidden" name="wind_forecast_2h_gust" id="f_w2g">
                <input type="hidden" name="wind_forecast_4h_speed" id="f_w4s">
                <input type="hidden" name="wind_forecast_4h_dir" id="f_w4d">
                <input type="hidden" name="wind_forecast_4h_gust" id="f_w4g">
                <input type="hidden" name="wind_forecast_6h_speed" id="f_w6s">
                <input type="hidden" name="wind_forecast_6h_dir" id="f_w6d">
                <input type="hidden" name="wind_forecast_6h_gust" id="f_w6g">

                <div class="row text-center mb-3 mt-3 d-none" id="gis_summary_box">
                    <div class="col-6 px-1">
                        <label class="d-block mb-1" style="font-size: 0.70rem; color: #1cc88a;">Area Stimata (Ha)</label>
                        <input type="text" class="form-control text-center font-weight-bold px-1" style="color: #1cc88a; border-color: #1cc88a;" id="ui_area_display" readonly placeholder="0">
                    </div>
                    <div class="col-6 px-1">
                        <label class="d-block mb-1" style="font-size: 0.70rem; color: #e74a3b;">Fronte (m)</label>
                        <input type="text" class="form-control text-center font-weight-bold px-1" style="color: #e74a3b; border-color: #e74a3b;" id="ui_front_display" readonly placeholder="0">
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="dos-label">Note Operative (Opzionale)</label>
                    <textarea class="form-control" name="notes" rows="3" placeholder="Es. Fronte esteso verso nord, fumo denso..."></textarea>
                </div>

                <button type="button" class="btn btn-send-report" id="btnSubmitReport">
                    <i class="fas fa-paper-plane mr-2"></i> Invia Report a SOUP/COP
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Mappa Overlay Fullscreen -->
<div id="mapOverlay">
    <div class="map-ui-top">
        <div class="stat-box">
            <div class="stat-val" id="ui_dist">---</div>
            <div class="stat-lbl">Distanza (m)</div>
        </div>
        <div class="stat-box border-left border-right">
            <div class="stat-val text-warning" id="ui_temp">--°C</div>
            <div class="stat-lbl">Meteo Live</div>
        </div>
        <div class="stat-box">
            <div class="stat-val text-info" id="ui_wind">-- km/h</div>
            <div class="stat-lbl text-info" id="ui_wind_dir_lbl">Vento N</div>
        </div>
    </div>

    <div id="mapContainer"></div>

    <div class="map-ui-bottom">
        <button class="btn btn-danger flex-fill py-3 shadow font-weight-bold" onclick="cancelMap()">
            <i class="fas fa-times"></i> Annulla
        </button>
        <button class="btn btn-success flex-fill py-3 shadow font-weight-bold" onclick="confirmMap()" id="btnConfirmMap" disabled>
            <i class="fas fa-check"></i> Conferma Posizione
        </button>
    </div>
</div>

<!-- Script Dipendenze -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let map = null;
    let watchId = null;

    // Coordinate & Markers
    let opLat = null, opLng = null;
    let fireLat = null, fireLng = null;
    let opMarker = null;
    let fireMarker = null;
    let lineSight = null;

    // Meteo Data
    let mTemp = null, mWind = null, mWindDir = null;

    // Icons
    const customOpIcon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div class='marker-compass' id='opCompass' style='color:#4e73df; font-size:24px; text-shadow: 0 2px 5px rgba(0,0,0,0.5);'><i class='fas fa-location-arrow'></i></div>",
        iconSize: [30, 42],
        iconAnchor: [15, 21]
    });

    const fireIcon = L.divIcon({
        className: 'custom-div-icon',
        html: "<div style='color:#e74a3b; font-size:32px; text-shadow: 0 4px 10px rgba(0,0,0,0.5); width:32px; height:32px; display:flex; justify-content:center; align-items:center; animation: pulse 1s infinite;'><i class='fas fa-fire'></i></div>",
        iconSize: [32, 42],
        iconAnchor: [16, 42]
    });

    // Inizializza mappa Fullscreen
    function openFullscreenMap() {
        document.getElementById('mapOverlay').style.display = 'block';

        // Wait for browser reflow before initializing Leaflet
        setTimeout(() => {
            if (!map) {
                // Init map layers
                let streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                });
                let satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: 'Tiles © Esri'
                });
                let hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                    maxZoom: 20,
                    subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                    attribution: '© Google'
                });

                // Centro Italia default approx
                map = L.map('mapContainer', {
                    zoomControl: false,
                    layers: [hybrid] // Default hybrid
                }).setView([39.0, 16.5], 8);

                // Add Layer Control
                let baseMaps = {
                    "Ibrido (Google)": hybrid,
                    "Satellitare (Esri)": satellite,
                    "Stradale (OSM)": streets
                };
                L.control.layers(baseMaps, null, {position: 'bottomright'}).addTo(map);

                // Add Compass custom control
                let CompassControl = L.Control.extend({
                    options: { position: 'topleft' },
                    onAdd: function(map) {
                        let div = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
                        div.innerHTML = '<div style="background:white; width: 44px; height: 44px; border-radius: 5px; box-shadow: 0 1px 5px rgba(0,0,0,0.65); display: flex; align-items: center; justify-content: center; flex-direction: column;"><i class="fas fa-caret-up text-danger mb-0" style="font-size:16px; line-height: 0.5;"></i><b style="font-size:12px; line-height: 1;">N</b></div>';
                        return div;
                    }
                });
                map.addControl(new CompassControl());

                // Add Click listener for fire (only if drawing is not active)
                map.on('click', function(e) {
                    setFireLocation(e.latlng.lat, e.latlng.lng);
                });

                // GIS Drawn Items
                let drawnItems = new L.FeatureGroup();
                map.addLayer(drawnItems);

                let drawControl = new L.Control.Draw({
                    draw: {
                        circle: false,
                        circlemarker: false,
                        marker: false,
                        rectangle: false,
                        polyline: { shapeOptions: { color: '#e74a3b', weight: 4 } },
                        polygon: { shapeOptions: { color: '#e74a3b', fillColor: '#e74a3b', fillOpacity: 0.4 } }
                    },
                    edit: {
                        featureGroup: drawnItems,
                        remove: true
                    }
                });
                map.addControl(drawControl);

                map.on(L.Draw.Event.CREATED, function (e) {
                    let type = e.layerType,
                        layer = e.layer;
                    drawnItems.clearLayers(); // Allow only one main feature per report
                    drawnItems.addLayer(layer);
                    calculateGISData(drawnItems);
                });

                map.on(L.Draw.Event.EDITED, function() { calculateGISData(drawnItems); });
                map.on(L.Draw.Event.DELETED, function() {
                    document.getElementById('f_polygon_geojson').value = '';
                    document.getElementById('f_area_hectares').value = '';
                    document.getElementById('f_front_meters').value = '';
                    document.getElementById('gis_summary_box').classList.add('d-none');
                });

                // Start GPS Watch
                startGpsTracking();

                // Start Compass Orientation
                if (window.DeviceOrientationEvent) {
                    window.addEventListener('deviceorientationabsolute', handleOrientation, true);
                }

                // Bulletproof fix for Leaflet grey tiles
                new ResizeObserver(() => {
                    if (map) map.invalidateSize();
                }).observe(document.getElementById('mapContainer'));
            }
        }, 100);
    }

    function calculateGISData(drawnItems) {
        let data = drawnItems.toGeoJSON();
        if(data.features.length === 0) return;

        let feature = data.features[0];
        let areaHa = 0;
        let lengthMeters = 0;

        if(feature.geometry.type === 'Polygon') {
            let areaSqM = turf.area(feature);
            areaHa = (areaSqM / 10000).toFixed(2);
            // Optionally, we could calculate the perimeter but the user wants Area.
        } else if(feature.geometry.type === 'LineString') {
            lengthMeters = (turf.length(feature, {units: 'meters'})).toFixed(0);
        }

        document.getElementById('f_polygon_geojson').value = JSON.stringify(feature);
        document.getElementById('f_area_hectares').value = areaHa > 0 ? areaHa : '';
        document.getElementById('f_front_meters').value = lengthMeters > 0 ? lengthMeters : '';

        document.getElementById('ui_area_display').value = areaHa > 0 ? areaHa : 0;
        document.getElementById('ui_front_display').value = lengthMeters > 0 ? lengthMeters : 0;
        document.getElementById('gis_summary_box').classList.remove('d-none');
    }

    function closeMap() {
        document.getElementById('mapOverlay').style.display = 'none';
        if (watchId) navigator.geolocation.clearWatch(watchId);
        window.removeEventListener('deviceorientationabsolute', handleOrientation, true);
    }

    function cancelMap() {
        closeMap();
    }

    function confirmMap() {
        if (!fireLat || !fireLng) {
            Swal.fire('Attenzione', 'Tocca la mappa per segnare la posizione dell\'incendio prima di confermare.', 'warning');
            return;
        }

        // Passa i dati al form sottostante
        document.getElementById('f_op_lat').value = (opLat || 0).toFixed(6);
        document.getElementById('f_op_lng').value = (opLng || 0).toFixed(6);
        document.getElementById('f_fire_lat').value = fireLat.toFixed(6);
        document.getElementById('f_fire_lng').value = fireLng.toFixed(6);

        // Calcola e popola distanza
        if(opLat && opLng) {
            let p1 = L.latLng(opLat, opLng);
            let p2 = L.latLng(fireLat, fireLng);
            let dist = Math.round(p1.distanceTo(p2));
            document.getElementById('f_distance').value = dist;
        }

        document.getElementById('f_temperature').value = mTemp !== null ? mTemp : 'N/A';
        document.getElementById('f_wind_speed').value = mWind !== null ? mWind : 'N/A';
        document.getElementById('f_wind_dir').value = mWindDir !== null ? mWindDir : 'N/A';

        closeMap();
    }

    function startGpsTracking() {
        if ("geolocation" in navigator) {
            watchId = navigator.geolocation.watchPosition((position) => {
                opLat = position.coords.latitude;
                opLng = position.coords.longitude;
                let accuracy = position.coords.accuracy;

                let latlng = [opLat, opLng];

                if (!opMarker) {
                    opMarker = L.marker(latlng, {icon: customOpIcon, zIndexOffset: 1000}).addTo(map);
                    // Center map on first lock only if fire isn't already set
                    if(!fireLat) map.setView(latlng, 15);
                } else {
                    opMarker.setLatLng(latlng);
                }

                updateSightLine();

            }, (error) => {
                console.warn("GPS Errore: ", error);
                if(error.code === 1) Swal.fire('Permessi GPS', 'Devi autorizzare il GPS per rilevare la tua posizione.', 'error');
            }, {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 10000
            });
        } else {
            Swal.fire('Errore', 'Il tuo dispositivo o browser non supporta la geolocalizzazione.', 'error');
        }
    }

    function handleOrientation(event) {
        let compass = event.webkitCompassHeading || Math.abs(event.alpha - 360);
        let el = document.getElementById('opCompass');
        if (el && compass) {
            // Ruota l'icona
            el.style.transform = `rotate(${compass}deg)`;
        }
    }

    function setFireLocation(lat, lng) {
        fireLat = lat;
        fireLng = lng;

        let latlng = [lat, lng];
        if (!fireMarker) {
            fireMarker = L.marker(latlng, {icon: fireIcon, draggable: true}).addTo(map);
            fireMarker.on('dragend', function(e) {
                let pos = fireMarker.getLatLng();
                setFireLocation(pos.lat, pos.lng);
            });
        } else {
            fireMarker.setLatLng(latlng);
        }

        document.getElementById('btnConfirmMap').removeAttribute('disabled');
        updateSightLine();
        fetchWeather(lat, lng);
        fetchMunicipalityData(lat, lng);
        fetchToponymsData(lat, lng);
    }

    function fetchMunicipalityData(lat, lng) {
        fireMarker.bindPopup('<i class="fas fa-spinner fa-spin"></i> Ricerca località esatta...').openPopup();

        const url = `{{ url('api/municipality/exact') }}?lat=${lat}&lon=${lng}`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if(data && data.success) {
                    let locStr = `<b>${data.city_name}</b><br><small>Provincia di ${data.province_abbr}</small>`;
                    fireMarker.bindPopup(`🔥 Incendio<br>${locStr}`).openPopup();

                    // Auto-select in dropdowns
                    let provSelect = document.getElementById('f_province');
                    provSelect.value = data.province_id;

                    // Trigger change to load cities manually or just set it
                    loadCities(data.province_id).then(() => {
                        document.getElementById('f_municipality').value = data.city_id;
                    });

                } else {
                    let distPopup = '';
                    if (opLat && opLng) {
                        let distMeters = Math.round(L.latLng(opLat, opLng).distanceTo(L.latLng(lat, lng)));
                        distPopup = `<br><span class="text-primary fw-bold" style="font-size:0.9rem;">Distanza: ${distMeters} m</span>`;
                    }
                    fireMarker.bindPopup(`Comune non trovato / Fuori Mappa${distPopup}`).openPopup();
                    document.getElementById('f_province').value = '';
                    document.getElementById('f_municipality').innerHTML = '<option value="">--- Seleziona prima Provincia ---</option>';
                }
            })
            .catch(e => {
                console.error("Municipality API Error:", e);
                fireMarker.bindPopup(`Errore ricerca distanza`).openPopup();
            });
    }

    function loadProvinces() {
        fetch(`{{ url('api/provinces') }}`)
            .then(r => r.json())
            .then(data => {
                let html = '<option value="">---</option>';
                data.forEach(p => {
                    html += `<option value="${p.id}">${p.short_code}</option>`;
                });
                document.getElementById('f_province').innerHTML = html;
            });
    }

    function loadCities(provId) {
        return fetch(`{{ url('api/cities') }}/${provId}`)
            .then(r => r.json())
            .then(data => {
                let html = '<option value="">--- Seleziona ---</option>';
                data.forEach(c => {
                    html += `<option value="${c.id}">${c.name}</option>`;
                });
                document.getElementById('f_municipality').innerHTML = html;
            });
    }

    // Initialize dropdowns and events
    document.addEventListener('DOMContentLoaded', function() {
        loadProvinces();

        document.getElementById('f_province').addEventListener('change', function() {
            let pId = this.value;
            if(pId) {
                loadCities(pId);
            } else {
                document.getElementById('f_municipality').innerHTML = '<option value="">--- Seleziona prima Provincia ---</option>';
            }
        });

        // Update hidden text inputs before submit
        document.getElementById('btnSubmitReport').addEventListener('click', function(e) {
            let pSel = document.getElementById('f_province');
            let cSel = document.getElementById('f_municipality');

            if (pSel.selectedIndex > 0) {
                document.getElementById('f_province_name').value = pSel.options[pSel.selectedIndex].text;
            }
            if (cSel.selectedIndex > 0) {
                document.getElementById('f_municipality_name').value = cSel.options[cSel.selectedIndex].text;
            }
        });
    });

    function fetchToponymsData(lat, lng) {
        let selectEl = document.getElementById('f_toponyms_display');
        selectEl.innerHTML = '<option value="">Ricerca toponimi in corso...</option>';

        const apiUrl = `{{ url('api/toponyms/nearest') }}?lat=${lat}&lon=${lng}`;
        fetch(apiUrl)
            .then(r => r.json())
            .then(data => {
                if (data && data.length > 0) {
                    let optionsHtml = "<option value=''>Seleziona il toponimo principale...</option>";
                    let limit = Math.min(data.length, 20); // Mostra fino a 20
                    for(let i=0; i<limit; i++) {
                        let km = parseFloat(data[i].distanza).toFixed(2);
                        optionsHtml += `<option value="${data[i].name}">${data[i].name} (${km} km)</option>`;
                    }
                    selectEl.innerHTML = optionsHtml;
                    document.getElementById('f_nearest_toponyms_json').value = JSON.stringify(data);
                } else {
                    selectEl.innerHTML = '<option value="">Nessun toponimo trovato nel raggio di 50km.</option>';
                    document.getElementById('f_nearest_toponyms_json').value = "";
                }
            })
            .catch(e => {
                console.error("Toponyms API Error:", e);
                selectEl.innerHTML = '<option value="">Errore durante il recupero dei toponimi.</option>';
            });
    }

    function updateSightLine() {
        if (opLat && opLng && fireLat && fireLng) {
            let p1 = L.latLng(opLat, opLng);
            let p2 = L.latLng(fireLat, fireLng);
            let dist = Math.round(p1.distanceTo(p2));

            if (lineSight) {
                map.removeLayer(lineSight);
            }
            lineSight = L.polyline([p1, p2], {color: '#e74a3b', dashArray: '5, 10', weight: 3}).addTo(map);

            document.getElementById('ui_dist').innerText = dist;
        }
    }

    // Integrazione Open-Meteo V1
    function fetchWeather(lat, lng) {
        document.getElementById('ui_temp').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Add hourly wind variables and daily ephemeris
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current_weather=true&hourly=windspeed_10m,winddirection_10m,windgusts_10m&daily=sunrise,sunset&timezone=auto`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                let cw = data.current_weather;
                if(cw) {
                    mTemp = cw.temperature;
                    mWind = cw.windspeed;
                    mWindDir = cw.winddirection;

                    document.getElementById('ui_temp').innerText = mTemp + '°C';
                    document.getElementById('ui_wind').innerText = mWind + ' km/h';
                    document.getElementById('ui_wind_dir_lbl').innerText = 'Dir. ' + getWindCompass(mWindDir);

                    // Display ephemeris
                    if(data.daily && data.daily.sunrise && data.daily.sunset) {
                        let sr = new Date(data.daily.sunrise[0]).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        let ss = new Date(data.daily.sunset[0]).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                        let ephemerisEl = document.getElementById('f_ephemeris');
                        if(ephemerisEl) {
                            ephemerisEl.innerHTML = `<i class="fas fa-sun text-warning"></i> Alba: <b>${sr}</b> | <i class="fas fa-moon text-secondary"></i> Tramonto: <b>${ss}</b>`;
                        }
                    }

                    // Display hourly wind forecasts (+2h, +4h, +6h)
                    if(data.hourly && data.hourly.time) {
                        // Find current hour index
                        let nowISO = new Date();
                        // Open-Meteo returns hourly time strings. Just find the closest hour.
                        let currentTime = nowISO.toISOString().substring(0, 13) + ":00"; // YYYY-MM-DDTHH:00
                        let currentIndex = data.hourly.time.findIndex(t => t.startsWith(currentTime));

                        if(currentIndex === -1) {
                            // fallback, find the first hour that is > now
                            currentIndex = data.hourly.time.findIndex(t => new Date(t) > nowISO);
                        }

                        if(currentIndex !== -1) {
                            let forecastHtml = '';
                            let offsets = [2, 4, 6];
                            offsets.forEach(offset => {
                                let idx = currentIndex + offset;
                                if(idx < data.hourly.time.length) {
                                    let dt = new Date(data.hourly.time[idx]);
                                    let timeStr = dt.getHours() + ':00';
                                    let ws = data.hourly.windspeed_10m[idx];
                                    let wg = data.hourly.windgusts_10m[idx] || 0;
                                    let wd = data.hourly.winddirection_10m[idx];
                                    let wdComp = getWindCompass(wd);

                                    forecastHtml += `
                                        <div class="col-4 px-1 text-center">
                                            <div class="small fw-bold text-muted">+${offset}h (${timeStr})</div>
                                            <div class="font-weight-bold" style="font-size:0.8rem;">
                                                <i class="fas fa-wind"></i> ${ws} km/h<br>
                                                <small class="text-danger">Raff: ${wg}</small><br>
                                                <span class="badge badge-info">${wdComp}</span>
                                            </div>
                                        </div>
                                    `;

                                    // Popola i campi hidden per il form
                                    if(offset === 2) {
                                        document.getElementById('f_w2s').value = ws;
                                        document.getElementById('f_w2d').value = wdComp;
                                        document.getElementById('f_w2g').value = wg;
                                    } else if(offset === 4) {
                                        document.getElementById('f_w4s').value = ws;
                                        document.getElementById('f_w4d').value = wdComp;
                                        document.getElementById('f_w4g').value = wg;
                                    } else if(offset === 6) {
                                        document.getElementById('f_w6s').value = ws;
                                        document.getElementById('f_w6d').value = wdComp;
                                        document.getElementById('f_w6g').value = wg;
                                    }
                                }
                            });

                            let fContainer = document.getElementById('wind_forecasts_container');
                            if(fContainer) {
                                fContainer.innerHTML = forecastHtml;
                            }
                        }
                    }
                }
            })
            .catch(e => {
                console.error("OpenMeteo Error:", e);
                document.getElementById('ui_temp').innerText = "Err";
            });
    }

    function getWindCompass(degrees) {
        if(degrees === null || degrees === undefined) return "N/A";
        const val = Math.floor((degrees / 22.5) + 0.5);
        const arr = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSW", "SW", "WSW", "W", "WNW", "NW", "NNW"];
        return arr[(val % 16)];
    }

    // Submit AJAX Form
    document.getElementById('btnSubmitReport').addEventListener('click', function() {

        if(!document.getElementById('f_fire_lat').value) {
            Swal.fire('Attenzione', 'Devi prima acquisire i dati dalla mappa.', 'warning');
            return;
        }

        let formData = new FormData(document.getElementById('dosReportForm'));

        Swal.fire({
            title: 'Invio Report in corso...',
            text: 'Trasmissione a SOUP e COP (Attendere)',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch('{{ route("dos.fire_management.send") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire('Inviato!', data.message, 'success')
                .then(()=> { window.location.href = "{{ route('dos.index') }}"; });
            } else {
                Swal.fire('Errore', data.message || 'Errore di connessione', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Errore critico', error.toString(), 'error');
        });
    });

</script>
<style>
    @keyframes pulse {
        0% { transform: scale(0.9); opacity: 0.8; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.8; }
    }
</style>
</x-app-layout>
