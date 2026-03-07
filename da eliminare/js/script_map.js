// nomefile: /js/script_map.js
// Scopo: gestione mappa Leaflet con disegno poligoni, calcolo area, centroide, perimetro, esportazione e invio via email

document.addEventListener('DOMContentLoaded', () => {
    const userDataElement = document.getElementById('user-data');
    if (!userDataElement) return;

    const email = userDataElement.getAttribute('data-email');
    const password = userDataElement.getAttribute('data-password');
    const siglaDos = userDataElement.getAttribute('data-sigla-dos') || 'N/A';
    const nomeCognomeDos = userDataElement.getAttribute('data-nome-cognome-dos') || 'N/A';

    window.totalArea = 0;
    window.totalFronteFuoco = 0;
    window.generalCentroid = null;
    window.polygons = [];
    window.drawnItems = new L.FeatureGroup();

    const map = L.map('map').setView([41.9028, 12.4964], 13);
    map.addControl(new L.Control.FullScreen());

    let currentLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: 'Google Satellite'
    }).addTo(map);

    map.addLayer(window.drawnItems);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            map.setView([lat, lon], 15);

            if (window.polygons.length === 0) {
                window.generalCentroid = { lat, lng: lon };
                document.getElementById('centroide-generale').textContent = convertToDMS(lat, lon);

                if (window.aggiornaComuneEProvincia) {
                    window.aggiornaComuneEProvincia(lat, lon);
                }

                if (typeof caricaLocalitaVicine === "function") {
                    caricaLocalitaVicine(lat, lon);
                }
            }
        }, () => {});
    }

    const centroidsLayer = new L.FeatureGroup();
    map.addLayer(centroidsLayer);

    L.drawLocal.draw.handlers.polygon.tooltip.start = '';
    L.drawLocal.draw.handlers.polygon.tooltip.cont = '';
    L.drawLocal.draw.handlers.polygon.tooltip.end = '';

    const drawControl = new L.Control.Draw({
        edit: { featureGroup: window.drawnItems },
        draw: {
            polygon: {
                title: '',
                shapeOptions: { color: '#ff0000' },
                metric: true,
                repeatMode: false
            },
            polyline: false,
            rectangle: false,
            circle: false,
            marker: false
        }
    });
    map.addControl(drawControl);

    const waitForDrawButton = setInterval(() => {
        const drawButton = document.querySelector('.leaflet-draw-draw-polygon');
        const drawActionBar = document.querySelector('.leaflet-draw-actions');

        if (drawButton) {
            clearInterval(waitForDrawButton);
            let isDrawing = false;

            map.on('draw:drawstart', () => {
                isDrawing = true;
                drawButton.classList.add('drawing-active');
                if (drawActionBar) drawActionBar.style.display = 'none';
            });

            map.on('draw:drawstop', () => {
                isDrawing = false;
                drawButton.classList.remove('drawing-active');
                if (drawActionBar) drawActionBar.style.display = 'none';
            });

            drawButton.addEventListener('click', () => {
                if (isDrawing) {
                    map.fire('draw:drawstop');
                }
            });

            const style = document.createElement('style');
            style.innerHTML = `
                .leaflet-draw-toolbar .leaflet-draw-draw-polygon.drawing-active {
                    background-color: #d32f2f !important;
                    border-color: #b71c1c !important;
                }
            `;
            document.head.appendChild(style);
        }
    }, 200);

    const deleteButton = document.querySelector('.leaflet-draw-edit-remove');
    deleteButton?.addEventListener('click', () => {
        window.drawnItems.clearLayers();
        centroidsLayer.clearLayers();
        window.polygons = [];
        window.totalArea = 0;
        window.totalFronteFuoco = 0;
        window.generalCentroid = null;

        document.getElementById('area-totale').textContent = '0 ha';
        document.getElementById('fronte-fuoco-totale').textContent = '0 m';
        document.getElementById('centroide-generale').textContent = 'N/A';
    });

    function calculateArea(latlngs) {
        let area = 0;
        for (let i = 0; i < latlngs.length - 1; i++) {
            const p1 = latlngs[i];
            const p2 = latlngs[i + 1];
            area += (p2.lng - p1.lng) * (p1.lat + p2.lat);
        }
        return Math.abs(area / 2) * 1000000;
    }

    function calculateLongestDiagonal(latlngs) {
        let maxDistance = 0;
        for (let i = 0; i < latlngs.length; i++) {
            for (let j = i + 1; j < latlngs.length; j++) {
                const distance = latlngs[i].distanceTo(latlngs[j]);
                if (distance > maxDistance) maxDistance = distance;
            }
        }
        return maxDistance;
    }

    function calculateCentroid(latlngs) {
        let x = 0, y = 0;
        for (const point of latlngs) {
            x += point.lat;
            y += point.lng;
        }
        return { lat: x / latlngs.length, lng: y / latlngs.length };
    }

    function calculateGeneralCentroid(polygons) {
        let x = 0, y = 0, count = 0;
        for (const p of polygons) {
            if (p.centroid) {
                x += p.centroid.lat;
                y += p.centroid.lng;
                count++;
            }
        }
        return count > 0 ? { lat: x / count, lng: y / count } : null;
    }

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

    function updatePolygonData(layer) {
        const latlngs = layer.getLatLngs()[0];
        const area = calculateArea(latlngs) / 10000;
        const fronteFuoco = calculateLongestDiagonal(latlngs);
        const centroid = calculateCentroid(latlngs);

        const index = window.drawnItems.getLayers().indexOf(layer);
        if (index === -1) return;

        if (!window.polygons[index]) window.polygons[index] = {};
        window.polygons[index].area = area;
        window.polygons[index].fronteFuoco = fronteFuoco;
        window.polygons[index].centroid = centroid;

        window.totalArea = window.polygons.reduce((sum, p) => sum + p.area, 0);
        window.totalFronteFuoco = window.polygons.reduce((sum, p) => sum + p.fronteFuoco, 0);
        window.generalCentroid = calculateGeneralCentroid(window.polygons);

        document.getElementById('area-totale').textContent = `${window.totalArea.toFixed(2)} ha`;
        document.getElementById('fronte-fuoco-totale').textContent = `${window.totalFronteFuoco.toFixed(2)} m`;
        document.getElementById('centroide-generale').textContent = window.generalCentroid
            ? convertToDMS(window.generalCentroid.lat, window.generalCentroid.lng)
            : 'N/A';

        centroidsLayer.clearLayers();
        window.polygons.forEach((p, i) => {
            L.circleMarker(p.centroid, {
                radius: 4,
                color: '#444',
                fillColor: '#fff',
                fillOpacity: 1,
                weight: 1
            })
            .bindTooltip(`Centroide Incendio ${i + 1}`)
            .addTo(centroidsLayer);
        });

        if (window.generalCentroid) {
            L.circleMarker(window.generalCentroid, {
                radius: 4,
                color: '#b71c1c',
                fillColor: '#d32f2f',
                fillOpacity: 1,
                weight: 1
            })
            .bindTooltip('Centroide Generale')
            .addTo(centroidsLayer);
        }
    }

    map.on(L.Draw.Event.CREATED, (event) => {
        const layer = event.layer;
        window.drawnItems.addLayer(layer);
        updatePolygonData(layer);
    });

    map.on(L.Draw.Event.EDITED, () => {
        window.drawnItems.eachLayer(layer => updatePolygonData(layer));
        if (window.generalCentroid && window.aggiornaComuneEProvincia) {
            window.aggiornaComuneEProvincia(window.generalCentroid.lat, window.generalCentroid.lng);
        }
    });

    document.getElementById('center-map').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                position => map.setView([position.coords.latitude, position.coords.longitude], 15),
                () => alert("Impossibile ottenere la posizione GPS. Assicurati che il GPS sia attivo.")
            );
        } else {
            alert("Il tuo browser non supporta la geolocalizzazione.");
        }
    });

    document.getElementById('map-view-selector').addEventListener('change', (event) => {
        const selectedView = event.target.value;
        map.removeLayer(currentLayer);

        if (selectedView === 'osm') {
            currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        } else if (selectedView === 'hybrid') {
            currentLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri'
            }).addTo(map);
        } else if (selectedView === 'satellite') {
            currentLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                attribution: 'Google Satellite'
            }).addTo(map);
        }
    });

    document.getElementById('export-kml').addEventListener('click', () => {
        import('./kml_handler.js').then(({ generateKML, sendKMLByEmail }) => {
            const provincia = document.getElementById('provincia-centroide')?.value || 'ND';
            const comune = document.getElementById('comune-centroide')?.value || 'ND';
            const localita = document.querySelector('#localita-container .localita-box strong')?.textContent || 'Nessuna';

            const { kml, fileName } = generateKML(
                siglaDos,
                nomeCognomeDos,
                window.totalArea,
                window.totalFronteFuoco,
                window.generalCentroid,
                window.polygons,
                window.drawnItems
            );

            sendKMLByEmail(
                kml,
                fileName,
                email,
                password,
                siglaDos,
                nomeCognomeDos,
                window.totalArea,
                window.totalFronteFuoco,
                window.generalCentroid,
                window.polygons
            );
        });
    });

    import('./localizzazione.js').then(({ inizializzaKML, aggiornaComuneEProvincia }) => {
        window.aggiornaComuneEProvincia = aggiornaComuneEProvincia;
        inizializzaKML(() => {
            if (window.generalCentroid) {
                aggiornaComuneEProvincia(window.generalCentroid.lat, window.generalCentroid.lng);
                if (typeof caricaLocalitaVicine === "function") {
                    caricaLocalitaVicine(window.generalCentroid.lat, window.generalCentroid.lng);
                }
            }
        });
    });
});
