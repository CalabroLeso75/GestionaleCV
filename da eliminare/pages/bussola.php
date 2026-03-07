<?php 
// ==============================
// File: /pages/bussola.php
// Scopo: visualizzazione bussola, posizione GPS, meteo e toponimi vicini
// ==============================
include("header.php"); 
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
  <title>Geolocalizzazione & Bussola</title>
  <link rel="stylesheet" href="../css/style.css?v=1">
</head>
<body>

<?php include("menubar.php"); ?>

<header class="header">
  <h1 class="title">🧭 Bussola e Posizione</h1>
</header>

<main class="conversion-container">
  <!-- Bussola dinamica -->
  <div id="compass-container">
    <img id="compass-img" src="compass.svg?v=1" alt="Bussola">
  </div>

  <!-- Azimut e vento -->
  <h3 id="azimut" class="label">Orientamento: --°</h3>
  <h3 id="vento" class="label">Vento: --</h3>
  <button id="attivaBussola" class="orange-button">Attiva Bussola</button>
  <button id="scegliPunto" class="orange-button">Scegli punto su mappa</button>

  <!-- Caricamento in corso -->
  <div id="loading" class="loading">
    <svg width="24" height="24" viewBox="0 0 100 100">
      <circle cx="50" cy="50" fill="none" stroke="#2196f3" stroke-width="10" r="35" stroke-dasharray="165 57">
        <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
      </circle>
    </svg>
    <span>In aggiornamento...</span>
  </div>

  <!-- Dati meteo, coordinate e toponimi -->
  <div id="geo-info" class="conversion-container text-box"></div>

  <div id="toponimi-vicini" class="conversion-container text-box" style="margin-top: 20px;">
    <h3>Località più vicine</h3>
    <div id="tabella-toponimi"></div>
  </div>
</main>

<?php include("../includes/footer.php"); ?>

<!-- Console di debug -->
<div class="debug-console">
  <strong>Debug:</strong>
  <pre id="debug-log"></pre>
</div>

<!-- Campi nascosti -->
<input type="hidden" id="provincia-centroide" name="provincia-centroide">
<input type="hidden" id="comune-centroide" name="comune-centroide">

<!-- Script -->
<script src="../js/toponimi.js?v=1"></script>
<script src="../js/alba_tramonto.js?v=1"></script> 
<script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
<script src="../js/bussola.js?v=1"></script>

<script>
// Array globali per i poligoni
let comuniPolygons = [];
let provincePolygons = [];

// Verifica se un punto è dentro un poligono
function pointInPolygon(point, vertices) {
  const x = point[0], y = point[1];
  let inside = false;
  for (let i = 0, j = vertices.length - 1; i < vertices.length; j = i++) {
    const xi = vertices[i][0], yi = vertices[i][1];
    const xj = vertices[j][0], yj = vertices[j][1];
    const intersect = ((yi > y) !== (yj > y)) &&
                      (x < (xj - xi) * (y - yi) / ((yj - yi) + 1e-10) + xi);
    if (intersect) inside = !inside;
  }
  return inside;
}

// Carica un file KML e lo converte in poligoni
function caricaKml(url, tipo) {
  return fetch(url)
    .then(response => response.text())
    .then(text => {
      const parser = new DOMParser();
      const kml = parser.parseFromString(text, "application/xml");
      const placemarks = kml.getElementsByTagName("Placemark");
      const data = [];

      for (let placemark of placemarks) {
        const name = placemark.getElementsByTagName("name")[0]?.textContent?.trim();
        const polygons = placemark.getElementsByTagName("Polygon");
        const feature = { nome: name, poligoni: [] };

        for (let polygon of polygons) {
          const outer = polygon.getElementsByTagName("outerBoundaryIs")[0]?.getElementsByTagName("coordinates")[0];
          if (!outer) continue;
          const outerCoords = outer.textContent.trim().split(/\s+/).map(c => {
            const [lon, lat] = c.split(',').map(Number);
            return [lon, lat];
          });

          const holes = [];
          const innerBoundaries = polygon.getElementsByTagName("innerBoundaryIs");
          for (let ib of innerBoundaries) {
            const coords = ib.getElementsByTagName("coordinates")[0];
            const inner = coords.textContent.trim().split(/\s+/).map(c => {
              const [lon, lat] = c.split(',').map(Number);
              return [lon, lat];
            });
            holes.push(inner);
          }

          feature.poligoni.push({ outer: outerCoords, holes });
        }

        data.push(feature);
      }

      if (tipo === "comuni") comuniPolygons = data;
      if (tipo === "province") provincePolygons = data;
    });
}

// Determina e aggiorna Comune e Provincia da coordinate
function updateComuneProvincia(lat, lon) {
  const point = [lon, lat];
  let comune = null, provincia = null;

  for (let prov of provincePolygons) {
    for (let poly of prov.poligoni) {
      if (pointInPolygon(point, poly.outer)) {
        if (!poly.holes.some(hole => pointInPolygon(point, hole))) {
          provincia = prov.nome;
          break;
        }
      }
    }
    if (provincia) break;
  }

  for (let com of comuniPolygons) {
    for (let poly of com.poligoni) {
      if (pointInPolygon(point, poly.outer)) {
        if (!poly.holes.some(hole => pointInPolygon(point, hole))) {
          comune = com.nome;
          break;
        }
      }
    }
    if (comune) break;
  }

  document.getElementById("comune-label").textContent = comune || "–";
  document.getElementById("provincia-label").textContent = provincia || "–";
  document.getElementById("display-comune-centroide").textContent = comune || "–";
  document.getElementById("display-provincia-centroide").textContent = provincia || "–";
  document.getElementById("comune-centroide").value = comune || "";
  document.getElementById("provincia-centroide").value = provincia || "";
}

// Inizializzazione KML al caricamento
Promise.all([
  caricaKml("comuni.kml", "comuni"),
  caricaKml("province.kml", "province")
]).then(() => {
  console.log("✅ KML caricati e convertiti");
});
</script>

</body>
</html>
