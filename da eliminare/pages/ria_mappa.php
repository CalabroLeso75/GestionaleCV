<!-- ria_mappa.php -->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Seleziona posizione sulla mappa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    #map {
      width: 100%;
      height: 100%;
      z-index: 1;
    }

    .info-box {
      position: absolute;
      bottom: 10px;
      left: 10px;
      background: rgba(255, 255, 255, 0.8);
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 1rem;
      z-index: 1000;
    }

    .control-buttons {
      position: absolute;
      bottom: 10px;
      right: 10px;
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      z-index: 1000;
    }

    .btn {
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
    }

    .confirm { background: #4caf50; color: white; }
    .cancel { background: #f44336; color: white; }A
    .locate { background: #2196f3; color: white; }
  </style>
</head>
<body>
  <div id="map"></div>
  <div class="info-box" id="info">Comune: -, Provincia: -, Lat: -, Lon: -</div>
  <div class="control-buttons">
    <button class="btn locate" onclick="getCurrentLocation()">📍</button>
    <button class="btn confirm" onclick="confirmPosition()">✅ Conferma Posizione</button>
    <button class="btn cancel" onclick="window.close()">❌ Chiudi</button>
  </div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    let map, marker, currentCoords = {};

    function toDMS(deg, isLat) {
      const dir = deg < 0 ? (isLat ? 'S' : 'W') : (isLat ? 'N' : 'E');
      const absDeg = Math.abs(deg);
      const d = Math.floor(absDeg);
      const mFloat = (absDeg - d) * 60;
      const m = Math.floor(mFloat);
      const s = ((mFloat - m) * 60).toFixed(2);
      return { d, m, s, dir };
    }

    function updateInfoBox(lat, lon, comune = '-', provincia = '-') {
      const latDMS = toDMS(lat, true);
      const lonDMS = toDMS(lon, false);
      currentCoords = {
        comune,
        provincia,
        latDeg: latDMS.d,
        latMin: latDMS.m,
        latSec: latDMS.s,
        lonDeg: lonDMS.d,
        lonMin: lonDMS.m,
        lonSec: lonDMS.s,
        quota: ''
      };
      document.getElementById("info").textContent = `Comune: ${comune}, Provincia: ${provincia}, Lat: ${latDMS.d}°${latDMS.m}'${latDMS.s}" ${latDMS.dir}, Lon: ${lonDMS.d}°${lonDMS.m}'${lonDMS.s}" ${lonDMS.dir}`;
    }

    function reverseGeocode(lat, lon) {
  fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
    .then(res => res.json())
    .then(data => {
      let comune = '-';
      let provincia = '-';

      try {
        const addr = data.address;
        comune = addr.town || addr.city || addr.village || addr.hamlet || '-';

        // Cerca la provincia nel campo display_name
        const display = data.display_name.toLowerCase();
        const provinceCalabria = {
          'cosenza': 'Cosenza',
          'catanzaro': 'Catanzaro',
          'crotone': 'Crotone',
          'vibo valentia': 'Vibo Valentia',
          'reggio calabria': 'Reggio Calabria'
        };

        for (const key in provinceCalabria) {
          if (display.includes(key)) {
            provincia = provinceCalabria[key];
            break;
          }
        }
      } catch (e) {
        console.warn("Errore nella lettura del comune/provincia:", e);
      }

      console.log("Comune:", comune, "Provincia:", provincia);
      updateInfoBox(lat, lon, comune, provincia);
      getAltitude(lat, lon);
    })
    .catch(err => {
      console.error("Errore durante il reverse geocoding:", err);
      updateInfoBox(lat, lon);
    });
}




    function getAltitude(lat, lon) {
  fetch(`https://api.open-elevation.com/api/v1/lookup?locations=${lat},${lon}`)
    .then(res => res.json())
    .then(data => {
      if (data.results && data.results.length > 0) {
        currentCoords.quota = Math.round(data.results[0].elevation);
        console.log("Quota rilevata:", currentCoords.quota);
      } else {
        console.warn("Quota non trovata");
      }
    })
    .catch(err => {
      console.error("Errore nella richiesta altitudine:", err);
    });
}


    function placeMarker(latlng) {
      if (marker) map.removeLayer(marker);
      marker = L.marker(latlng).addTo(map);
      reverseGeocode(latlng.lat, latlng.lng);
    }

    function getCurrentLocation() {
      if (!navigator.geolocation) {
        alert("Geolocalizzazione non supportata dal browser.");
        return;
      }
      navigator.geolocation.getCurrentPosition(pos => {
        const { latitude, longitude } = pos.coords;
        const latlng = L.latLng(latitude, longitude);
        map.setView(latlng, 15);
        placeMarker(latlng);
      }, err => {
        alert("Errore nella geolocalizzazione: " + err.message);
      });
    }

    function confirmPosition() {
      if (!currentCoords.latDeg || !window.opener) {
        alert("Seleziona una posizione valida sulla mappa.");
        return;
      }
	  
	  // MOSTRA I DATI CHE VERRANNO PASSATI
  console.log("Dati inviati alla pagina principale:", currentCoords);
  
      if (window.opener && typeof window.opener.aggiornaDatiDaMappa === 'function') {
        window.opener.aggiornaDatiDaMappa(currentCoords);
        window.close();
      }
    }

    map = L.map('map').setView([38.9, 16.3], 9);
    const sat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
      attribution: 'Tiles © Esri'
    });
    sat.addTo(map);

    map.on('click', e => {
      placeMarker(e.latlng);
    });

    getCurrentLocation();
  </script>
</body>
</html>
