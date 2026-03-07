let placemarks = [];

function decimalToDMS(decimal, isLat) {
    let degrees = Math.floor(Math.abs(decimal));
    let minutes = Math.floor((Math.abs(decimal) - degrees) * 60);
    let seconds = ((Math.abs(decimal) - degrees - minutes / 60) * 3600).toFixed(2);
    let direction = isLat ? (decimal >= 0 ? "N" : "S") : (decimal >= 0 ? "E" : "W");
    return { degrees, minutes, seconds, direction };
}

function getCurrentPosition() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const lat = decimalToDMS(position.coords.latitude, true);
            const lng = decimalToDMS(position.coords.longitude, false);

            document.getElementById("lat_deg").value = lat.degrees;
            document.getElementById("lat_min").value = lat.minutes;
            document.getElementById("lat_sec").value = lat.seconds;
            document.getElementById("lat_dir").value = lat.direction;

            document.getElementById("lng_deg").value = lng.degrees;
            document.getElementById("lng_min").value = lng.minutes;
            document.getElementById("lng_sec").value = lng.seconds;
            document.getElementById("lng_dir").value = lng.direction;

            alert("📍 Posizione corrente acquisita con successo!");
        }, error => {
            alert("Errore nel rilevamento della posizione: " + error.message);
        });
    } else {
        alert("Geolocalizzazione non supportata dal browser.");
    }
}

function dmsToDecimal(degrees, minutes, seconds, direction) {
    let decimal = degrees + minutes / 60 + seconds / 3600;
    if (direction === "S" || direction === "W") {
        decimal = -decimal;
    }
    return decimal;
}

function haversineDistance(coord1, coord2) {
    const R = 6371;
    const toRad = x => x * Math.PI / 180;
    const dLat = toRad(coord2.lat - coord1.lat);
    const dLng = toRad(coord2.lng - coord1.lng);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(toRad(coord1.lat)) * Math.cos(toRad(coord2.lat)) *
              Math.sin(dLng / 2) * Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function findNearestPoints(reference, points) {
    let validPoints = points.map(point => {
        const distanceKm = haversineDistance(reference, point);
        if (isNaN(distanceKm)) return null; // Scarta il punto se la distanza è NaN

        const distanceNM = (distanceKm * 0.539957).toFixed(2);
        const timeMinutes = ((distanceNM * 2) / 70) * 60;
        const totalTimeMinutes = timeMinutes + (100 / 60);
        const rotationsIn130Minutes = (130 / totalTimeMinutes).toFixed(2);

        return {
            ...point,
            distanceNM: isNaN(distanceNM) ? "Errore" : distanceNM,
            timeMinutes: isNaN(totalTimeMinutes) ? "Errore" : totalTimeMinutes.toFixed(2),
            rotations: isNaN(rotationsIn130Minutes) ? "Errore" : rotationsIn130Minutes
        };
    })
    .filter(point => point !== null) // Rimuove i risultati null
    .sort((a, b) => a.distanceNM - b.distanceNM)
    .slice(0, 10);

    return validPoints;
}


function processCoordinates() {
    if (placemarks.length === 0) {
        alert("Errore: il file KML non è stato caricato correttamente.");
        return;
    }

    const lat = dmsToDecimal(
        parseFloat(document.getElementById("lat_deg").value),
        parseFloat(document.getElementById("lat_min").value),
        parseFloat(document.getElementById("lat_sec").value),
        document.getElementById("lat_dir").value
    );

    const lng = dmsToDecimal(
        parseFloat(document.getElementById("lng_deg").value),
        parseFloat(document.getElementById("lng_min").value),
        parseFloat(document.getElementById("lng_sec").value),
        document.getElementById("lng_dir").value
    );

    if (isNaN(lat) || isNaN(lng)) {
        alert("Inserisci coordinate valide!");
        return;
    }

    const referencePoint = { lat, lng };
    const nearestPoints = findNearestPoints(referencePoint, placemarks);

    let resultsHTML = `<h3>🔹 I 10 punti più vicini:</h3><ul style="text-align: left;">`;
    nearestPoints.forEach(p => {
        const dmsLat = decimalToDMS(p.lat, true);
        const dmsLng = decimalToDMS(p.lng, false);
        const googleMapsLink = `https://www.google.com/maps/@${p.lat},${p.lng},19z?layer=t&q=${p.lat},${p.lng}`;

        resultsHTML += `
        <li>
            <strong>📍 ${p.name}</strong><br>
            🗺️ <strong>Coordinate:</strong> ${dmsLat.degrees}°${dmsLat.minutes}'${dmsLat.seconds}" ${dmsLat.direction},  
            ${dmsLng.degrees}°${dmsLng.minutes}'${dmsLng.seconds}" ${dmsLng.direction}  
            <br>🌍 <a href="${googleMapsLink}" target="_blank">Visualizza su Google Maps</a>
            <br>📏 <strong>Distanza:</strong> ${p.distanceNM} NM
            <br>⏱️ <strong>Tempo di rotazione:</strong> ${p.timeMinutes} min
            <br>🔄 <strong>Rotazioni in 130 min:</strong> ${p.rotations}
        </li><br>`;
    });
    resultsHTML += "</ul>";

    document.getElementById("results").innerHTML = resultsHTML;
}


function loadKML() {
    fetch("BaciniAcqua.kml")
        .then(response => response.text())
        .then(text => {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(text, "text/xml");

            placemarks = Array.from(xmlDoc.getElementsByTagName("Placemark")).map(pm => {
                const name = pm.getElementsByTagName("name")[0]?.textContent || "Senza nome";
                const coords = pm.getElementsByTagName("coordinates")[0]?.textContent.trim().split(",");
                return { name, lat: parseFloat(coords[1]), lng: parseFloat(coords[0]) };
            });

            console.log("KML caricato con successo:", placemarks.length, "punti trovati.");
        })
        .catch(error => console.error("Errore nel caricamento del file KML:", error));
}
document.addEventListener("DOMContentLoaded", function () {
    var menu = document.getElementById("menu");
    var toggleButton = document.querySelector(".menu-toggle");
    var hideMenuButton = document.querySelector(".hide-menu");

    if (toggleButton && hideMenuButton && menu) {
        toggleButton.addEventListener("click", function () {
            menu.style.display = "block";
            toggleButton.style.display = "none";
        });

        hideMenuButton.addEventListener("click", function () {
            menu.style.display = "none";
            toggleButton.style.display = "block";
        });
    } else {
        console.error("⚠️ Errore: elementi del menu non trovati!");
    }
});

window.onload = loadKML;
