<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'] ?? 'soup@calabriaverde.eu';
    $subject = $_POST['subject'] ?? '(Nessun oggetto)';
    $body = $_POST['body'] ?? '';
    $fromName = $_POST['fromName'] ?? 'Fire Master';
    $fromEmail = $_POST['fromEmail'] ?? 'noreply@calabriaverde.eu';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtps.aruba.it';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@calabriaverde.eu'; // Sostituisci con email valida
        $mail->Password = 'TUA_PASSWORD';             // Sostituisci con password reale
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($to);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Gestione allegati (anche multipli)
        if (!empty($_FILES['attachments'])) {
            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileTmp  = $_FILES['attachments']['tmp_name'][$key];
                    $fileName = $_FILES['attachments']['name'][$key];
                    $mail->addAttachment($fileTmp, $fileName);
                }
            }
        }

        $mail->send();
        echo "OK: Email inviata con successo.";
    } catch (Exception $e) {
        http_response_code(500);
        echo "ERRORE: " . $mail->ErrorInfo;
    }
} else {
    http_response_code(405);
    echo "ERRORE: Metodo non consentito.";
}
?>
