<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';

    if (!isset($_FILES['attachment']) || $_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo "Errore nel caricamento del file allegato.";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Configurazione server SMTP Aruba
        $mail->isSMTP();
        $mail->Host = 'smtps.aruba.it';
        $mail->SMTPAuth = true;
        $mail->Username = 'TUA_EMAIL@aruba.it'; // <-- Sostituisci con la tua email Aruba
        $mail->Password = 'TUA_PASSWORD';       // <-- Sostituisci con la tua password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Mittente e destinatario
        $mail->setFrom('TUA_EMAIL@aruba.it', 'Fire Master');
        $mail->addAddress($to);

        // Contenuto
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Allegato
        $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);

        $mail->send();
        echo "Email inviata con successo.";
    } catch (Exception $e) {
        http_response_code(500);
        echo "Errore nell'invio: {$mail->ErrorInfo}";
    }
} else {
    http_response_code(405);
    echo "Metodo non consentito.";
}
?>
