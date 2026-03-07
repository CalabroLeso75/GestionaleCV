// ==============================
// File: /js/kml_handler.js
// Scopo: Generazione file KML e invio via email
// Autore: Raffaele Bruno Cusano
// ==============================

// Funzione per creare il file KML contenente i poligoni disegnati sulla mappa
export function generateKML(siglaDos, nomeCognomeDos, totalArea, totalFronteFuoco, generalCentroid, polygons, drawnItems) {
    const [nome, cognome] = nomeCognomeDos.split(' ');

    const now = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const fileName = `${siglaDos}_${nome}_${cognome}_${now.getFullYear()}${pad(now.getMonth() + 1)}${pad(now.getDate())}_${pad(now.getHours())}${pad(now.getMinutes())}.kml`;

    const polygonLayers = drawnItems.getLayers().filter(layer => layer instanceof L.Polygon);
    if (!polygonLayers || polygonLayers.length === 0 || !generalCentroid) {
        return { kml: null, fileName: null };
    }

    let kml = `<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Document>
    <name>${fileName}</name>
    <description><![CDATA[
      <strong>Sigla DOS:</strong> ${siglaDos}<br>
      <strong>Nome e Cognome DOS:</strong> ${nome} ${cognome}<br>
      <strong>Area Totale:</strong> ${totalArea.toFixed(2)} ha<br>
      <strong>Fronte del Fuoco Totale:</strong> ${totalFronteFuoco.toFixed(2)} m<br>
      <strong>Centroide Generale:</strong> ${convertToDMS(generalCentroid.lat, generalCentroid.lng)}<br>
      <strong>Provincia:</strong> ${document.getElementById('provincia-centroide')?.value || 'ND'}<br>
      <strong>Comune:</strong> ${document.getElementById('comune-centroide')?.value || 'ND'}<br>
      <strong>Località Indicativa:</strong> ${document.querySelector('#localita-container .localita-box strong')?.textContent || 'Nessuna'}
    ]]></description>
    <Style id="polygonStyle">
      <LineStyle><color>ff000000</color><width>3</width></LineStyle>
      <PolyStyle><color>7f00ffff</color></PolyStyle>
    </Style>
    ${polygonLayers.map((layer, index) => {
        const latlngs = layer.getLatLngs();
        const p = polygons.find(polygonData => {
            const centroid = polygonData.centroid;
            const match = centroid && Math.abs(centroid.lat - calculateCentroid(latlngs[0]).lat) < 0.0001 &&
                                      Math.abs(centroid.lng - calculateCentroid(latlngs[0]).lng) < 0.0001;
            return match;
        }) || {};
        return `<Placemark>
      <name>Incendio ${index + 1}</name>
      <styleUrl>#polygonStyle</styleUrl>
      <description><![CDATA[
        <strong>Area:</strong> ${p?.area?.toFixed(2) || 'N/A'} ha<br>
        <strong>Fronte del Fuoco:</strong> ${p?.fronteFuoco?.toFixed(2) || 'N/A'} m<br>
        <strong>Centroide:</strong> ${p?.centroid ? convertToDMS(p.centroid.lat, p.centroid.lng) : 'N/A'}
      ]]></description>
      <Polygon>
        <outerBoundaryIs><LinearRing><coordinates>
          ${latlngs[0].map(latlng => `${latlng.lng.toFixed(6)},${latlng.lat.toFixed(6)},0`).join(' ')}
          ${(latlngs[0].length > 0 && !(latlngs[0][0].equals(latlngs[0][latlngs[0].length - 1]))) ? `${latlngs[0][0].lng.toFixed(6)},${latlngs[0][0].lat.toFixed(6)},0` : ''}
        </coordinates></LinearRing></outerBoundaryIs>
        ${latlngs.slice(1).map(innerRing => `
        <innerBoundaryIs><LinearRing><coordinates>
          ${innerRing.map(latlng => `${latlng.lng.toFixed(6)},${latlng.lat.toFixed(6)},0`).join(' ')}
          ${(innerRing.length > 0 && !(innerRing[0].equals(innerRing[innerRing.length - 1]))) ? `${innerRing[0].lng.toFixed(6)},${innerRing[0].lat.toFixed(6)},0` : ''}
        </coordinates></LinearRing></innerBoundaryIs>`).join('')}
      </Polygon>
    </Placemark>`;
    }).join('')}
  </Document>
</kml>`;

    return { kml, fileName };
}

// Funzione di supporto per il calcolo del centroide di un poligono
function calculateCentroid(latlngs) {
    let x = 0, y = 0;
    latlngs.forEach(point => {
        x += point.lat;
        y += point.lng;
    });
    return { lat: x / latlngs.length, lng: y / latlngs.length };
}

// Invio del file KML via fetch POST a send_mail_map.php
export function sendKMLByEmail(kml, fileName, email, password, siglaDos, nomeCognomeDos, totalArea, totalFronteFuoco, generalCentroid, polygons) {
    const provincia = document.getElementById('provincia-centroide')?.value || '';
    const comune = document.getElementById('comune-centroide')?.value || '';
    const localita = document.querySelector('#localita-container .localita-box strong')?.textContent || 'Nessuna';

    fetch('send_mail_map.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            kml,
            fileName,
            email,
            password,
            siglaDos,
            nomeCognomeDos,
            totalArea,
            totalFronteFuoco,
            generalCentroid,
            polygons,
            provincia,
            comune,
            localitaIndicativa: localita
        })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              alert('File KML inviato con successo!');
          } else {
              const errorMessageDiv = document.getElementById('error-message');
              errorMessageDiv.textContent = `Errore: ${data.message}\nDettagli: ${data.error_details}`;
              errorMessageDiv.style.display = 'block';
              setTimeout(() => { errorMessageDiv.style.display = 'none'; }, 5000);
          }
      });
}

// Conversione coordinate in formato DMS leggibile
function convertToDMS(lat, lng) {
    function toDMS(coord, isLat) {
        const dir = coord >= 0 ? (isLat ? 'N' : 'E') : (isLat ? 'S' : 'W');
        const absCoord = Math.abs(coord);
        const deg = Math.floor(absCoord);
        const min = Math.floor((absCoord - deg) * 60);
        const sec = ((absCoord - deg - min / 60) * 3600).toFixed(2);
        return `${deg}°${min}'${sec}"${dir}`;
    }
    return `${toDMS(lat, true)}, ${toDMS(lng, false)}`;
}
