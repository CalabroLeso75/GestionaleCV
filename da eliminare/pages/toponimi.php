<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Toponimi Vicini</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <style>
    #map {{ height: 500px; margin-bottom: 20px; }}
    table {{ border-collapse: collapse; width: 100%; }}
    th, td {{ border: 1px solid #ccc; padding: 8px; text-align: left; }}
  </style>
</head>
<body>
  <h1>Seleziona un punto sulla mappa</h1>
  <div id="map"></div>
  <h2>Toponimi più vicini</h2>
  <table id="results">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Latitudine</th>
        <th>Longitudine</th>
        <th>Distanza (km)</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    const map = L.map('map').setView([38.5, 16.0], 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {{
      maxZoom: 18,
      attribution: '© OpenStreetMap contributors'
    }}).addTo(map);

    let marker;
    map.on('click', function(e) {{
      const lat = e.latlng.lat;
      const lon = e.latlng.lng;

      if (marker) {{
        marker.setLatLng(e.latlng);
      }} else {{
        marker = L.marker(e.latlng).addTo(map);
      }}

      const vicini = trovaToponimiVicini(lat, lon);
      const tbody = document.querySelector('#results tbody');
      tbody.innerHTML = '';
      vicini.forEach(p => {{
        const row = `<tr><td>${{p.name}}</td><td>${{p.latitude.toFixed(5)}}</td><td>${{p.longitude.toFixed(5)}}</td><td>${{p.distanza.toFixed(2)}}</td></tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
      }});
    }});
  </script>
</body>
</html>
