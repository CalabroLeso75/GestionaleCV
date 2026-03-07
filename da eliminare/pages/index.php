<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (
        $data &&
        isset($data['timestamp'], $data['userAgent'], $data['platform'], $data['deviceMemory'],
              $data['hardwareConcurrency'], $data['language'], $data['screenResolution'])
    ) {
        if (!file_exists("logs")) {
            mkdir("logs", 0777, true);
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown_ip';
        $log_entry = $data['timestamp'] . " | IP: " . $ip .
                     " | OS: " . $data['platform'] .
                     " | Agent: " . $data['userAgent'] .
                     " | Mem: " . $data['deviceMemory'] .
                     " | CPU: " . $data['hardwareConcurrency'] .
                     " | Lang: " . $data['language'] .
                     " | Screen: " . $data['screenResolution'] . "\n";

        file_put_contents("logs/consensi_accettati.log", $log_entry, FILE_APPEND);
        http_response_code(200);
        exit;
    } else {
        http_response_code(400);
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
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        #consensoModal {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.85); color: #fff;
            display: flex; justify-content: center; align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background: #222; padding: 30px; border-radius: 10px;
            max-width: 700px; text-align: left;
        }
        .modal-content h2 { margin-top: 0; }
        .modal-content button {
            padding: 10px 20px; background: #4CAF50;
            border: none; border-radius: 5px; color: white; cursor: pointer;
        }
    </style>
</head>
<body>

<div id="consensoModal" style="display:none;">
    <div class="modal-content">
        <h2>🔐 Informativa sull'accesso e trattamento delle credenziali</h2>
        <p>Questa piattaforma è riservata al personale autorizzato, in particolare ai DOS accreditati.</p>
        <p>Per accedere è necessario inserire le credenziali della propria casella email istituzionale. Le credenziali sono usate solo per:</p>
        <ul>
            <li>Inviare richieste RIA (Richiesta Intervento Aereo)</li>
            <li>Trasmettere file KML delle aree disegnate sulla mappa</li>
        </ul>
        <p><strong>Le credenziali:</strong></p>
        <ul>
            <li>Non vengono salvate in modo permanente</li>
            <li>Vengono cancellate al logout o alla scadenza della sessione</li>
        </ul>
        <p>L’utente è responsabile dell’utilizzo delle proprie credenziali. L’accettazione costituisce conferma di presa visione.</p>
        <label><input type="checkbox" id="accettaCondizioni"> Dichiaro di aver letto e accettato le condizioni sopra riportate</label><br><br>
        <button onclick="salvaConsenso()">Accetto</button>
    </div>
</div>

<script>
function salvaConsenso() {
    const accettato = document.getElementById('accettaCondizioni').checked;
    if (!accettato) {
        alert("Devi accettare le condizioni per continuare.");
        return;
    }

    const data = {
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent,
        platform: navigator.platform || "unknown",
        deviceMemory: navigator.deviceMemory ? navigator.deviceMemory + "GB" : "unknown",
        hardwareConcurrency: navigator.hardwareConcurrency ? navigator.hardwareConcurrency + " cores" : "unknown",
        language: navigator.language || "unknown",
        screenResolution: screen.width + "x" + screen.height
    };

    localStorage.setItem("consenso_firemaster", JSON.stringify(data));

    fetch(window.location.href, {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).then(() => {
        document.getElementById("consensoModal").style.display = "none";
        window.location.href = "dostools.php";
    });
}

window.onload = function() {
    const consenso = localStorage.getItem("consenso_firemaster");
    if (consenso) {
        window.location.href = "dostools.php";
    } else {
        document.getElementById("consensoModal").style.display = "flex";
    }
}
</script>

</body>
</html>
