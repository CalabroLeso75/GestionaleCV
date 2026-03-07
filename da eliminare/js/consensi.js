/* ================================
   File: /js/consensi.js
   Scopo: Gestisco l'interazione del consenso
   (salvataggio in localStorage, invio fetch, redirect)
   ================================= */

function salvaConsenso() {
    const accettato = document.getElementById('accettaCondizioni').checked;
    if (!accettato) {
        alert("Devi accettare le condizioni per continuare.");
        return;
    }

    const data = {
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent
    };

    // Salvo il consenso localmente nel browser
    localStorage.setItem("consenso_firemaster", JSON.stringify(data));

    // Invia i dati al server per registrarli nel file di log
    fetch(window.location.href, {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(() => {
        // Dopo l'invio, nascondo il modale e reindirizzo alla pagina principale
        document.getElementById("consensoModal").style.display = "none";
        window.location.href = "dostools.php";
    });
}

window.onload = function() {
    // Se il consenso è già stato salvato, reindirizzo direttamente
    const consenso = localStorage.getItem("consenso_firemaster");
    if (consenso) {
        window.location.href = "dostools.php";
    } else {
        document.getElementById("consensoModal").style.display = "flex";
    }
};
