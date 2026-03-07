// /js/misura_distanza.js
export function inizializzaMisuraDistanza(mappa, getIsDrawing) {
    let posizioneMarker = null;
    let markerMisura = null;
    let linea = null;
    let distanzaLabel = null;

    if (!mappa) {
        console.error("La mappa non è definita in misura_distanza.js");
        return;
    }

    navigator.geolocation.getCurrentPosition(posizione => {
        const userLatLng = [posizione.coords.latitude, posizione.coords.longitude];

        // Marker fisso per la posizione dell'utente
        posizioneMarker = L.marker(userLatLng, {
            icon: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconSize: [24, 24]
            })
        }).addTo(mappa);

        // Marker mobile spostabile
        markerMisura = L.marker(userLatLng, {
            draggable: true,
            icon: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448444.png',
                iconSize: [28, 28]
            })
        }).addTo(mappa);

        // Linea iniziale tra i due marker
        linea = L.polyline([userLatLng, userLatLng], { color: 'blue' }).addTo(mappa);

        // Tooltip per la distanza
        distanzaLabel = L.tooltip({
            permanent: true,
            direction: 'center',
            className: 'distance-tooltip'
        }).setContent("0 m").setLatLng(userLatLng).addTo(mappa);

        // Evento drag sul marker mobile
        markerMisura.on('drag', function (e) {
            if (getIsDrawing && getIsDrawing()) return;

            const targetLatLng = e.target.getLatLng();
            const origine = posizioneMarker.getLatLng();

            linea.setLatLngs([origine, targetLatLng]);

            const distanza = origine.distanceTo(targetLatLng);
            distanzaLabel.setLatLng(getMidpoint(origine, targetLatLng));
            distanzaLabel.setContent(`${distanza.toFixed(1)} m`);
        });
    }, () => {
        console.warn("Posizione utente non disponibile.");
    });

    function getMidpoint(p1, p2) {
        return L.latLng((p1.lat + p2.lat) / 2, (p1.lng + p2.lng) / 2);
    }
}
