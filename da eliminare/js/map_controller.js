// ==============================
// File: /js/map_controller.js
// Scopo: gestione della visualizzazione delle località vicine al centroide
// Autore: Raffaele Bruno Cusano
// ==============================

function caricaLocalitaVicine(lat, lon) {
    const container = document.getElementById("localita-container");
    const messaggioErrore = document.getElementById("error-message");

    container.innerHTML = "";
    messaggioErrore.style.display = "none";

    fetch("ricerca_toponimi.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ lat: lat, lon: lon })
    })
    .then(response => {
        if (!response.ok) throw new Error("Errore nella risposta del server.");
        return response.json();
    })
    .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
            container.innerHTML = "<p>Nessuna località trovata.</p>";
            return;
        }

        data.forEach(loc => {
            const card = document.createElement("div");
            card.style.padding = "12px";
            card.style.border = "1px solid #ccc";
            card.style.borderRadius = "6px";
            card.style.backgroundColor = "#fff";
            card.innerHTML = `
                <strong>${loc.name}</strong><br>
                <span style="font-size: 0.9em;">${loc.distance.toFixed(1)} km</span>
            `;
            container.appendChild(card);
        });
    })
    .catch(error => {
        messaggioErrore.textContent = "Errore nel caricamento delle località vicine.";
        messaggioErrore.style.display = "block";
    });
}

window.caricaLocalitaVicine = caricaLocalitaVicine;
