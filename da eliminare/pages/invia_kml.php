<?php
// nome file: /pages/invia_kml.php
require_once 'config/email_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kml'])) {
    $kmlContent = $_POST['kml'];

    // Dati aggiuntivi dalla mappa
    $filename = $_POST['filename'] ?? 'mappa.kml';
    $area = $_POST['area'] ?? 'n.d.';
    $perimetro = $_POST['perimetro'] ?? 'n.d.';
    $centroide = $_POST['centroide'] ?? 'n.d.';

    $message = <<<EOD
In allegato il file KML esportato dalla mappa.

📐 Area: {$area} m²
📏 Perimetro: {$perimetro} m
📍 Centroide (lng, lat): {$centroide}
EOD;

    // Salva temporaneamente il file KML
    $tempFile = tempnam(sys_get_temp_dir(), 'kml_');
    file_put_contents($tempFile, $kmlContent);

    // Prepara l'email
    $to = EMAIL_DEST_DEFAULT;
    $cc = EMAIL_CC;
    $subject = 'File KML esportato';
    $separator = md5(time());
    $eol = PHP_EOL;

    // Leggi il contenuto del file
    $attachment = chunk_split(base64_encode(file_get_contents($tempFile)));

    // Costruisci l'intestazione dell'email
    $headers = "From: webmaster@example.com" . $eol;
    $headers .= "CC: $cc" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

    // Costruisci il corpo dell'email
    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"utf-8\"" . $eol;
    $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
    $body .= $message . $eol;

    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/vnd.google-earth.kml+xml; name=\"" . $filename . "\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"" . $eol . $eol;
    $body .= $attachment . $eol;
    $body .= "--" . $separator . "--";

    // Invia l'email
    if (mail($to, $subject, $body, $headers)) {
        echo "Email inviata con successo.";
    } else {
        echo "Errore nell'invio dell'email.";
    }

    // Elimina il file temporaneo
    unlink($tempFile);
} else {
    echo "Nessun dato KML ricevuto.";
}
?>
