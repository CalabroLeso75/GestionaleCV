<?php
// ================================
// File: /pages/map.php
// Scopo: Pagina mappa per disegno incendi, invio KML e determinazione posizione
// Autore: Raffaele Bruno Cusano
// ================================

session_start();

$email = $_SESSION['email'] ?? 'N/A';
$password = $_SESSION['password'] ?? 'N/A';
$siglaDos = $_SESSION['sigla'] ?? 'N/A';
$nomeCognomeDos = ($_SESSION['nome'] ?? 'N/A') . ' ' . ($_SESSION['cognome'] ?? 'N/A');

include 'header.php';
include 'menubar.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Mappa Interattiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Librerie CSS di Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.css">
    <link rel="stylesheet" href="../css/style.css">

    <!-- Librerie JS esterne -->
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script src="https://unpkg.com/togeojson@0.16.0"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
    <script src="https://unpkg.com/leaflet.fullscreen@1.6.0/Control.FullScreen.js"></script>

    <!-- Script personalizzati -->
    <script src="../js/script_map.js" defer></script>
    <script src="../js/toponimi.js" defer></script>

    <!-- Script di inizializzazione moduli KML -->
    <script type="module">
        // Importo le funzioni per inizializzare i file KML dei comuni e aggiornare i dati
        import { inizializzaKML, aggiornaComuneEProvincia } from './localizzazione.js';
        inizializzaKML(() => {
            if (window.generalCentroid) {
                aggiornaComuneEProvincia(window.generalCentroid.lat, window.generalCentroid.lng);
                if (typeof caricaLocalitaVicine === 'function') {
                    caricaLocalitaVicine(window.generalCentroid.lat, window.generalCentroid.lng);
                }
            }
        });
        window.aggiornaComuneEProvincia = aggiornaComuneEProvincia;
    </script>

    <style>
        #info {
            border-left: 4px solid darkgreen;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            width: fit-content;
            margin: 20px auto;
            text-align: left;
        }
        #info h3 {
            text-align: center;
            margin-top: 0;
            color: darkred;
        }
        #info p {
            margin: 6px 0;
        }
    </style>
</head>
<body>

<div class="conversion-container">
    <h1>Mappa Navigabile</h1>

    <!-- Pulsanti di controllo mappa -->
    <div class="menu-container" style="margin-bottom: 20px;">
        <button id="center-map" class="vertical-button">Centra Mappa</button>
        <button id="clear-map" class="vertical-button">Cancella Tutto</button>
        <select id="map-view-selector" class="vertical-select">
            <option value="osm">Mappa (OpenStreetMap)</option>
            <option value="hybrid">Ibrida</option>
            <option value="satellite">Satellitare</option>
        </select>
    </div>

    <!-- Contenitore mappa principale -->
    <div id="map" style="height: 400px; width: 100%; margin-top: 20px; border: 1px solid #ccc; border-radius: 8px;"></div>

    <!-- Dettagli calcolati relativi ai poligoni disegnati -->
    <div id="info">
        <h3>Dati Poligoni</h3>
        <p><strong>Sigla DOS:</strong> <span id="sigla-dos"><?php echo $siglaDos; ?></span></p>
        <p><strong>Nome e Cognome DOS:</strong> <span id="nome-cognome-dos"><?php echo $nomeCognomeDos; ?></span></p>
        <p><strong>Email Utente:</strong> <span id="user-email"><?php echo $email; ?></span></p>
        <p><strong>Area Totale:</strong> <span id="area-totale">0 ha</span></p>
        <p><strong>Fronte del Fuoco Totale:</strong> <span id="fronte-fuoco-totale">0 m</span></p>
        <p><strong>Centroide Generale:</strong> <span id="centroide-generale">N/A</span></p>

        <!-- Selezione manuale della provincia -->
        <p><strong>Provincia:</strong><br>
            <select id="provincia-centroide" style="width: 250px;">
                <option value="">--</option>
                <option value="CS">Cosenza</option>
                <option value="CZ">Catanzaro</option>
                <option value="KR">Crotone</option>
                <option value="RC">Reggio Calabria</option>
                <option value="VV">Vibo Valentia</option>
            </select>
        </p>

        <!-- Inserimento o aggiornamento manuale del comune -->
        <p><strong>Comune:</strong><br>
            <input type="text" id="comune-centroide" style="width: 250px;">
        </p>
    </div>

    <!-- Pulsante di esportazione KML -->
    <button id="export-kml" class="vertical-button" style="margin-top: 20px;">Esporta KML</button>

    <!-- Dati utente per invio email -->
    <div id="user-data"
         data-email="<?php echo htmlspecialchars($email); ?>"
         data-password="<?php echo htmlspecialchars($password); ?>"
         data-sigla-dos="<?php echo htmlspecialchars($siglaDos); ?>"
         data-nome-cognome-dos="<?php echo htmlspecialchars($nomeCognomeDos); ?>"
         style="display: none;"></div>

    <!-- Messaggi di errore -->
    <div id="error-message" class="error-message" style="color: red; margin-top: 10px; text-align: center; display: none;"></div>

    <!-- Elenco località trovate vicino al centroide -->
    <div id="localita-vicine" style="margin-top: 20px;">
        <h2>Località Vicine</h2>
        <div id="localita-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;"></div>
    </div>
</div>

<!-- Aggiunta della funzionalità fullscreen alla mappa -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof L.Control.Fullscreen === "function") {
            const checkMap = setInterval(() => {
                if (window.map || typeof map !== "undefined") {
                    const mappa = window.map || map;
                    mappa.addControl(new L.Control.Fullscreen());
                    clearInterval(checkMap);
                }
            }, 200);
        }
    });
</script>

<!-- Rimuove l'autocompletamento del campo comune se modificato manualmente -->
<script>
    document.getElementById("comune-centroide").addEventListener("input", function () {
        this.dataset.auto = "no";
    });
</script>

<!-- Osserva cambiamenti del centroide e aggiorna le informazioni -->
<script type="module">
    import { aggiornaComuneEProvincia } from './localizzazione.js';
    const spanCentroide = document.getElementById("centroide-generale");
    const observer = new MutationObserver(() => {
        if (window.generalCentroid) {
            aggiornaComuneEProvincia(window.generalCentroid.lat, window.generalCentroid.lng);
            caricaLocalitaVicine(window.generalCentroid.lat, window.generalCentroid.lng);
        }
    });
    if (spanCentroide) observer.observe(spanCentroide, { childList: true });
</script>

<!-- Caricamento delle località vicine tramite chiamata fetch -->
<script>
    function caricaLocalitaVicine(lat, lon) {
        const container = document.getElementById("localita-container");
        container.innerHTML = "<p>Caricamento in corso...</p>";
        fetch(`ricerca_toponimi.php?lat=${lat}&lon=${lon}`)
            .then(res => res.json())
            .then(dati => {
                if (dati.error) {
                    container.innerHTML = `<p style='color:red;'>Errore: ${dati.error}</p>`;
                    return;
                }
                if (dati.length === 0) {
                    container.innerHTML = "<p>Nessuna località trovata.</p>";
                    return;
                }
                container.innerHTML = "";
                dati.forEach(loc => {
                    const div = document.createElement("div");
                    div.className = "localita-box";
                    div.innerHTML = `<strong>${loc.name}</strong><br>Distanza: ${loc.distanza.toFixed(2)} km<br>Coordinate: ${loc.latitude.toFixed(5)}, ${loc.longitude.toFixed(5)}`;
                    container.appendChild(div);
                });
            })
            .catch(() => {
                container.innerHTML = `<p style='color:red;'>Errore nella richiesta</p>`;
            });
    }
</script>

</body>
</html>
