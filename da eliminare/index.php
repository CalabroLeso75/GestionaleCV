<?php
// ==========================
// File: /index.php
// Funzione: gestisco la schermata iniziale per accettazione consenso
// e scrivo i log dei consensi in un file locale
// ==========================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ricevo il corpo JSON della richiesta
    $data = json_decode(file_get_contents("php://input"), true);

    // Controllo che i dati siano validi
    if ($data && isset($data['timestamp'], $data['userAgent'])) {
        // Creo la cartella dei log se non esiste
        if (!file_exists("logs")) {
            mkdir("logs", 0777, true);
        }

        // Apro il file di log in modalità aggiunta
        $file = fopen("logs/consensi_accettati.log", "a");

        // Scrivo la riga di log con data, IP e user agent
        fwrite($file, date("Y-m-d H:i:s") . " | " . $_SERVER['REMOTE_ADDR'] . " | " . $data['userAgent'] . " | " . $data['timestamp'] . "\n");

        fclose($file);
        http_response_code(200); // Rispondo con OK
        exit;
    } else {
        http_response_code(400); // Dati non validi
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Accesso FireMaster</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Includo lo stile del modale -->
    <link rel="stylesheet" href="css/consensi.css">
</head>
<body>

<!-- Modale che chiede il consenso -->
<div id="consensoModal" style="display:none;">
    <div class="modal-content">
        <h2>🔐 Informativa sull'accesso e utilizzo del sistema</h2>
        <p>Per accedere alle funzionalità della piattaforma, è necessario inserire le credenziali della propria casella di posta elettronica istituzionale. Tali credenziali vengono utilizzate esclusivamente per:</p>
        <ul>
            <li>Inviare richieste di intervento aereo (RIA)</li>
            <li>Trasmettere file KML delle aree disegnate sulla mappa</li>
        </ul>
        <p>Le credenziali non vengono salvate in modo permanente e sono cancellate al logout o alla scadenza della sessione.</p>
        <label><input type="checkbox" id="accettaCondizioni"> Dichiaro di aver letto e accettato le condizioni</label><br><br>
        <button onclick="salvaConsenso()">Accetto</button>
    </div>
</div>

<!-- Includo lo script JS per la gestione del consenso -->
<script src="js/consensi.js"></script>

</body>
</html>
