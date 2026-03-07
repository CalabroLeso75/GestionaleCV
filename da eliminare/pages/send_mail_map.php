<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Ricevi i dati dal frontend
$data = json_decode(file_get_contents('php://input'), true);

// Debug: logga tutto l'array ricevuto
error_log("📥 Payload ricevuto: " . print_r($data, true));

$kml = $data['kml'];
$email = $data['email'];
$password = $data['password'];
$siglaDos = strtoupper(str_replace(' ', '', $data['siglaDos']));
$nomeCognomeDos = $data['nomeCognomeDos'];
$totalArea = $data['totalArea'] ?? 0;
$totalFronteFuoco = $data['totalFronteFuoco'] ?? 0;
$generalCentroid = $data['generalCentroid'] ?? null;
$provincia = $data['provincia'] ?? '';
$comune = $data['comune'] ?? '';
$localita = $data['localitaIndicativa'] ?? 'Nessuna';

// Log specifici per i campi chiave
error_log("🔍 Sigla DOS: $siglaDos");
error_log("🔍 Nome Cognome DOS: $nomeCognomeDos");
error_log("🗺️ Provincia: $provincia | Comune: $comune | Località: $localita");
error_log("📊 Area: $totalArea ha | Fronte: $totalFronteFuoco m");
if ($generalCentroid) {
    error_log("📍 Centroide: lat=" . $generalCentroid['lat'] . ", lng=" . $generalCentroid['lng']);
} else {
    error_log("⚠️ Centroide non disponibile");
}

// Funzione per convertire coordinate in formato DMS
function convertToDMS($lat, $lng) {
    function toDMS($coord, $isLat) {
        $dir = $coord >= 0 ? ($isLat ? 'N' : 'E') : ($isLat ? 'S' : 'W');
        $absCoord = abs($coord);
        $deg = floor($absCoord);
        $min = floor(($absCoord - $deg) * 60);
        $sec = (($absCoord - $deg - $min / 60) * 3600);
        return sprintf("%d°%d'%.2f\"%s", $deg, $min, $sec, $dir);
    }
    return toDMS($lat, true) . ', ' . toDMS($lng, false);
}

// Funzione per creare nome file KML
function creaNomeFileKML($provincia, $comune, $siglaDos) {
    $data = new DateTime("now", new DateTimeZone("Europe/Rome"));
    $timestamp = $data->format("Ymd_Hi");
    $comuneSanificato = preg_replace('/[^A-Za-z0-9]/', '_', iconv('UTF-8', 'ASCII//TRANSLIT', $comune));
    return "{$timestamp}_{$provincia}_{$comuneSanificato}_{$siglaDos}.kml";
}

$nomeFileKML = $data['fileName'] ?? creaNomeFileKML($provincia, $comune, $siglaDos);

// Corpo dell'email
$body = "Rapporto operativo generato automaticamente dal sistema.\n\n";
$body .= "Dati del DOS:\n- Sigla: {$siglaDos}\n- Nome e Cognome: {$nomeCognomeDos}\n\n";
$body .= "Informazioni Geografiche:\n";
$body .= "- Comune: {$comune}\n";
$body .= "- Provincia: {$provincia}\n";
$body .= "- Località indicativa: {$localita} (dato approssimativo calcolato dal centroide)\n\n";
$body .= "Totali:\n";
$body .= "- Superficie Totale Bruciata: " . number_format($totalArea, 2) . " ha\n";
$body .= "- Lunghezza Totale dei Fronti Incendio: " . number_format($totalFronteFuoco, 2) . " m\n";
$body .= "- Punto Centrale Generale: " . ($generalCentroid ? convertToDMS($generalCentroid['lat'], $generalCentroid['lng']) : 'N/A') . "\n";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtps.aruba.it';
    $mail->SMTPAuth = true;
    $mail->Username = $email;
    $mail->Password = $password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom($email, 'Mappa Interattiva');
    $mail->addAddress('soup@calabriaverde.eu');
    $mail->addBCC($email);
    $mail->addBCC('mappe@calabriaverde.eu');

    $mail->isHTML(false);
    $mail->Subject = 'Rapporto Operativo DOS: ' . $siglaDos . ' - ' . $nomeCognomeDos;
    $mail->Body = $body;
    $mail->addStringAttachment($kml, $nomeFileKML);

    error_log("📧 Invio email con oggetto: {$mail->Subject}");

    $mail->send();
    error_log("✅ Email inviata con successo.");

    echo json_encode(['success' => true, 'message' => 'Email inviata con successo.']);
} catch (Exception $e) {
    $errorMessage = $mail->ErrorInfo;
    error_log("❌ Errore durante l'invio dell'email: " . $errorMessage);

    echo json_encode([
        'success' => false,
        'message' => 'Errore durante l\'invio dell\'email.',
        'error_details' => $errorMessage
    ]);
}
?>
