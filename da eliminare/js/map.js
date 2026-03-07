// Nome file: /js/map.js

document.addEventListener("DOMContentLoaded", function () {
    // Inizializza la mappa centrata sulla Calabria
    const map = L.map("map").setView([38.5, 16.5], 8);

    // Layer delle mappe base
    const tileLayers = {
        osm: L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "© OpenStreetMap contributors"
        }),
        satellite: L.tileLayer("https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}", {
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "© Google"
        }),
        hybrid: L.tileLayer("https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}", {
            subdomains: ["mt0", "mt1", "mt2", "mt3"],
            attribution: "© Google"
        })
    };

    // Aggiungi la mappa OSM iniziale
    tileLayers.osm.addTo(map);

    // Cambia mappa da menu a tendina
    document.getElementById("mapType").addEventListener("change", function () {
        const tipo = this.value;
        map.eachLayer(layer => {
            if (layer instanceof L.TileLayer) map.removeLayer(layer);
        });
        tileLayers[tipo].addTo(map);
    });

    // Gruppo poligoni disegnati
    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    // Icona personalizzata
    const customIcon = L.icon({
        iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
    });

    // Controlli per disegno
    const drawControl = new L.Control.Draw({
        edit: { featureGroup: drawnItems },
        draw: {
            polygon: { showArea: true },
            marker: { icon: customIcon },
            polyline: false,
            rectangle: false,
            circle: false,
            circlemarker: false
        }
    });
    map.addControl(drawControl);

    // Mostra area e perimetro
    const info = document.getElementById("info");

    map.on(L.Draw.Event.CREATED, function (e) {
        const layer = e.layer;
        drawnItems.clearLayers();
        drawnItems.addLayer(layer);
        if (e.layerType === "polygon") {
            const latlngs = layer.getLatLngs()[0];
            latlngs.push(latlngs[0]); // chiude
            const polygon = turf.polygon([latlngs.map(p => [p.lng, p.lat])]);
            const area = (turf.area(polygon) / 10000).toFixed(2); // ettari
            const perimeter = (turf.length(polygon, { units: "kilometers" })).toFixed(2); // km
            info.innerHTML = `📐 <strong>Area:</strong> ${area} ha &nbsp; | &nbsp; <strong>Perimetro:</strong> ${perimeter} km`;
        }
    });

    // Centra sulla posizione dell’utente
    document.getElementById("centerMap").addEventListener("click", () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                map.setView([lat, lng], 14);
                L.marker([lat, lng], { icon: customIcon })
                    .addTo(map)
                    .bindPopup("La tua posizione attuale")
                    .openPopup();
            }, err => {
                alert("Errore: " + err.message);
            });
        } else {
            alert("Il browser non supporta la geolocalizzazione.");
        }
    });

// Esporta KML e invia a invia_kml.php
document.getElementById("exportKML").addEventListener("click", () => {
    const geojson = drawnItems.toGeoJSON();
    if (!geojson.features.length) {
        alert("Disegna almeno un poligono.");
        return;
    }

    let totaleArea = 0;
    let totalePerimetro = 0;
    const centroids = [];
    let dettaglio = "";

    geojson.features.forEach((feature, index) => {
        const coordinates = feature.geometry.coordinates[0];
        const polygon = turf.polygon([coordinates]);
        const area = turf.area(polygon); // in m²
        const perimetro = turf.length(polygon, { units: "meters" }); // in m
        const centroide = turf.centroid(polygon).geometry.coordinates; // [lng, lat]

        // Accumula
        totaleArea += area;
        totalePerimetro += perimetro;
        centroids.push(centroide);

        // Formatta output
        const area_ha = (area / 10000).toFixed(2);
        const perimetro_m = perimetro.toFixed(2);
        const centroide_str = formatCoordinate(centroide);

        dettaglio += `Incendio ${index + 1}:\n`;
        dettaglio += `- Area: ${area_ha} ha\n`;
        dettaglio += `- Fronte del Fuoco: ${perimetro_m} m\n`;
        dettaglio += `- Centroide: ${centroide_str}\n\n`;
    });

    // Calcolo centroide generale medio
    const centroideGenerale = calcolaCentroideMedia(centroids);
    const centroideGeneraleStr = formatCoordinate(centroideGenerale);

    const areaTotaleHa = (totaleArea / 10000).toFixed(2);
    const perimetroTotaleM = totalePerimetro.toFixed(2);

    const messaggio = `Rapporto operativo generato automaticamente dal sistema.
Questo messaggio contiene i dati totali relativi agli incendi registrati,
inclusi la superficie totale bruciata, la lunghezza totale del fronte incendio e il punto centrale generale.

Totale:
- Superficie Totale Bruciata: ${areaTotaleHa} ha
- Lunghezza Totale dei Fronti Incendio: ${perimetroTotaleM} m
- Centroide Generale: ${centroideGeneraleStr}

Dettagli per ogni incendio:
${dettaglio}`;

    const kml = tokml(geojson);
    const filename = "intervento_" + new Date().toISOString().slice(0, 10) + ".kml";

    fetch("invia_kml.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            kml: kml,
            filename: filename,
            messaggio: messaggio
        })
    })
    .then(r => r.text())
    .then(alert)
    .catch(err => alert("Errore invio: " + err));
});

// Funzione per convertire [lng, lat] in gradi decimali N/E
function formatCoordinate(coord) {
    function toDegMin(val, isLat) {
        const dir = isLat ? (val >= 0 ? "N" : "S") : (val >= 0 ? "E" : "W");
        const abs = Math.abs(val);
        const deg = Math.floor(abs);
        const min = ((abs - deg) * 60).toFixed(2);
        return `${deg}°${min}'${dir}`;
    }
    return `${toDegMin(coord[1], true)}, ${toDegMin(coord[0], false)}`;
}

// Calcolo media dei centri
function calcolaCentroideMedia(centroids) {
    const somma = centroids.reduce((acc, val) => {
        acc[0] += val[0];
        acc[1] += val[1];
        return acc;
    }, [0, 0]);
    return [somma[0] / centroids.length, somma[1] / centroids.length];
}


});
