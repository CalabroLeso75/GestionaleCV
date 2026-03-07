// File: /js/bussola.js
// Gestione bussola, meteo, altitudine, alba/tramonto, toponimi e localizzazione KML

// Elementi DOM principali
const compassImg = document.getElementById("compass-img");
const azimutText = document.getElementById("azimut");
const ventoText = document.getElementById("vento");
const geoInfo = document.getElementById("geo-info");
const debugLog = document.getElementById("debug-log");
const attivaBtn = document.getElementById("attivaBussola");
const btnScegli = document.getElementById("scegliPunto");

let ultimoHeading = null;
let ultimaLatitudine = null;
let ultimaLongitudine = null;
let posizioneDispositivo = null;
let posizioneIncendio = null;

// Apertura mappa selezione punto
if (btnScegli) {
  btnScegli.addEventListener("click", () => {
    const mappa = window.open("seleziona_punto_mappa.html", "_blank", "width=400,height=700");
    mappa.addEventListener("load", () => {
      mappa.postMessage("addBaseLayerButtons", "*");
    });
  });
}

// Ricezione coordinate dal selettore mappa
window.addEventListener("message", (event) => {
  if (!event.data || !event.data.lat || !event.data.lng) return;
  ultimaLatitudine = event.data.lat;
  ultimaLongitudine = event.data.lng;
  posizioneIncendio = { lat: ultimaLatitudine, lng: ultimaLongitudine };
  aggiornaBloccoDistanza();
  logDebug(`Coordinate manuali ricevute: ${ultimaLatitudine}, ${ultimaLongitudine}`);

  fetch(`https://api.open-elevation.com/api/v1/lookup?locations=${ultimaLatitudine},${ultimaLongitudine}`)
    .then(res => res.json())
    .then(json => {
      const altitudine = json.results?.[0]?.elevation ?? null;
      mostraCoordinate({ coords: { latitude: ultimaLatitudine, longitude: ultimaLongitudine, altitude: altitudine } });
    })
    .catch(() => {
      mostraCoordinate({ coords: { latitude: ultimaLatitudine, longitude: ultimaLongitudine, altitude: null } });
    });
});

// Traduzione angolo in direzione vento italiana
function ventoInItaliano(gradi) {
  const nomi = [
    "Tramontana", "Tramontana-Grecale", "Grecale", "Grecale-Levante",
    "Levante", "Levante-Scirocco", "Scirocco", "Scirocco-Ostro",
    "Ostro", "Ostro-Libeccio", "Libeccio", "Libeccio-Ponente",
    "Ponente", "Ponente-Maestrale", "Maestrale", "Maestrale-Tramontana"
  ];
  return nomi[Math.round(gradi / 22.5) % 16];
}

// Aggiunge messaggio alla console debug
function logDebug(msg) {
  if (debugLog) debugLog.textContent += msg + "\n";
  console.log("[DEBUG]", msg);
}

// Aggiorna rotazione bussola
function updateCompass(heading) {
  ultimoHeading = heading;
  compassImg.style.transform = `rotate(${-heading}deg)`;
  azimutText.textContent = `Orientamento: ${Math.round(heading)}°`;
  ventoText.textContent = `Vento: ${ventoInItaliano(heading)}`;
}

// Gestione orientamento bussola
function handleOrientation(event) {
  let heading = event.alpha;
  if (typeof event.webkitCompassHeading !== 'undefined') {
    heading = event.webkitCompassHeading;
  }
  if (heading !== null) {
    updateCompass(heading);
  } else {
    logDebug("Nessun valore heading disponibile");
  }
}

// Attivazione bussola
attivaBtn.addEventListener("click", () => {
  if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
    DeviceOrientationEvent.requestPermission().then(response => {
      if (response === 'granted') {
        window.addEventListener("deviceorientation", handleOrientation);
        attivaBtn.style.display = 'none';
      }
    });
  } else {
    window.addEventListener("deviceorientation", handleOrientation);
    attivaBtn.style.display = 'none';
  }
});

// Converte coordinate decimali in DMS
function gradiToDMS(gradi, tipo) {
  const assoluto = Math.abs(gradi);
  const g = Math.floor(assoluto);
  const m = Math.floor((assoluto - g) * 60);
  const s = (((assoluto - g) * 60 - m) * 60).toFixed(2);
  const d = tipo === 'lat' ? (gradi >= 0 ? 'N' : 'S') : (gradi >= 0 ? 'E' : 'W');
  return `${g}°${m}'${s}" ${d}`;
}

// Mostra coordinate e info meteo
function mostraCoordinate(pos) {
  const { latitude, longitude, altitude } = pos.coords;
  ultimaLatitudine = latitude;
  ultimaLongitudine = longitude;

  if (!posizioneDispositivo) {
    posizioneDispositivo = { lat: latitude, lng: longitude };
  }

  posizioneIncendio = { lat: latitude, lng: longitude };
  aggiornaBloccoDistanza();

  const latDMS = gradiToDMS(latitude, 'lat');
  const lonDMS = gradiToDMS(longitude, 'lon');
  const quota = (altitude !== null) ? `${altitude.toFixed(2)} m` : 'non disponibile';
  const now = new Date();

  const alba = typeof calcolaAlba === 'function' ? calcolaAlba(latitude, longitude, now) : '--:--';
  const tramonto = typeof calcolaTramonto === 'function' ? calcolaTramonto(latitude, longitude, now) : '--:--';

  fetch(`https://wttr.in/${latitude},${longitude}?format=j1`)
    .then(r => r.json())
    .then(meteo => {
      const current = meteo.current_condition[0];
      const timestamp = now.toLocaleString('it-IT');

      mostraToponimi(latitude, longitude);

      const content = `
        <p><strong>Coordinate:</strong><br>${latDMS}<br>${lonDMS}</p>
        <p><strong>Altitudine:</strong> ${quota}</p>
        <p><strong>Comune:</strong> <span id="comune-label" class="comune-prov">–</span></p>
        <p><strong>Provincia:</strong> <span id="provincia-label" class="comune-prov">–</span></p>
        <p><strong>Alba:</strong> ${alba}</p>
        <p><strong>Tramonto:</strong> ${tramonto}</p>
        <p><strong>Velocità vento:</strong> ${current.windspeedKmph || '--'} km/h</p>
        <p><strong>Raffica vento:</strong> ${current.windgustKmph || '--'} km/h</p>
        <p><strong>Temperatura aria:</strong> ${current.temp_C || '--'} °C</p>
        <p><strong>Temperatura percepita:</strong> ${current.FeelsLikeC || '--'} °C</p>
        <p style="margin-top:1em;font-style:italic;font-size:0.9em;">Ultimo aggiornamento: ${timestamp}</p>`;

      geoInfo.innerHTML = content;
	  localStorage.setItem("ultimoDati", content);
	  document.getElementById("loading").style.display = 'none';
	  aggiornaBloccoDistanza();

      if (typeof updateComuneProvincia === 'function') {
        updateComuneProvincia(latitude, longitude);
      }
    })
    .catch(() => {
      document.getElementById("loading").style.display = 'none';
    });
}

// Mostra toponimi vicini
function mostraToponimi(lat, lon) {
  if (typeof placemarks === 'undefined') return;

  const vicini = trovaToponimiVicini(lat, lon, 20);
  const tabellaDiv = document.getElementById('tabella-toponimi');

  if (!Array.isArray(vicini)) {
    tabellaDiv.innerHTML = "<p>Errore nel caricamento dei toponimi.</p>";
    return;
  }

  if (vicini.length === 0) {
    tabellaDiv.innerHTML = "<p>Nessun toponimo trovato vicino alla tua posizione.</p>";
    return;
  }

  let html = '<table style="width:100%; border-collapse:collapse;">';
  vicini.forEach((toponimo, index) => {
    const coloreSfondo = index % 2 === 0 ? '#f9f9f9' : '#ffffff';
    html += `
      <tr style="background-color:${coloreSfondo};">
        <td style="text-align:left; padding:8px;">${toponimo.name}</td>
        <td style="text-align:right; padding:8px;">${(toponimo.distanza * 1000).toFixed(0)} m</td>
      </tr>`;
  });
  html += '</table>';
  tabellaDiv.innerHTML = html;
}

// Determina comune e provincia
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

  document.getElementById("comune-centroide").value = comune || "";
  document.getElementById("provincia-centroide").value = provincia || "";

  const comuneLabel = document.getElementById("comune-label");
  const provinciaLabel = document.getElementById("provincia-label");
  if (comuneLabel) comuneLabel.textContent = comune || "–";
  if (provinciaLabel) provinciaLabel.textContent = provincia || "–";
}

// Recupera posizione iniziale
function getPosition() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(mostraCoordinate, err => {
      logDebug("Errore geolocalizzazione: " + err.message);
    }, { enableHighAccuracy: true });
  }
}

window.onload = getPosition;

// ↪️ Nuova funzione in fondo
function aggiornaBloccoDistanza() {
  if (!posizioneDispositivo || !posizioneIncendio) return;

  const dist = turf.distance(
    turf.point([posizioneDispositivo.lng, posizioneDispositivo.lat]),
    turf.point([posizioneIncendio.lng, posizioneIncendio.lat]),
    { units: 'kilometers' }
  );

  const dist_m = (dist * 1000).toFixed(1);

  const coordInc = `${gradiToDMS(posizioneIncendio.lat, 'lat')}<br>${gradiToDMS(posizioneIncendio.lng, 'lon')}`;
  const coordDos = `${gradiToDMS(posizioneDispositivo.lat, 'lat')}<br>${gradiToDMS(posizioneDispositivo.lng, 'lon')}`;

  const blocco = `
    <div id="blocco-distanza" style="margin-top:1em; padding-top:1em; border-top:1px dashed #aaa;">
      <p><strong>Coordinate Incendio:</strong><br>${coordInc}</p>
      <p><strong>Coordinate del DOS:</strong><br>${coordDos}</p>
      <p><strong>Distanza dall'incendio:</strong> ${dist_m} m</p>
    </div>
  `;

  const contenitore = document.getElementById("geo-info");
  const esistente = document.getElementById("blocco-distanza");

  if (esistente) {
    esistente.outerHTML = blocco;
  } else {
    contenitore.insertAdjacentHTML("beforeend", blocco);
  }
}
