<?php
session_start();
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $errore = '';

    // 1. Verifica credenziali SMTP
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = getSMTPHost($email);
        $mail->SMTPAuth = true;
        $mail->Username = $email;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getSMTPPort($email);
        $mail->smtpConnect(); // connessione senza invio email

    } catch (Exception $e) {
        $errore = "Credenziali della casella email non valide.";
    }

    // 2. Se la connessione SMTP è riuscita, controlla nel DB
    if (empty($errore)) {
        $db = new SQLite3('../data/gestione_utenti.db');
        $stmt = $db->prepare("SELECT * FROM utenti WHERE email = :email");
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute();
        $utente = $result->fetchArray(SQLITE3_ASSOC);

        if ($utente) {
            // Sessione utente
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            $_SESSION['nome'] = $utente['nome'];
            $_SESSION['cognome'] = $utente['cognome'];
            $_SESSION['sigla'] = $utente['sigla'];
            $_SESSION['telefono'] = $utente['telefono'];
            $_SESSION['organizzazione'] = $utente['organizzazione'];
            $_SESSION['ruolo'] = $utente['ruolo'];
            $_SESSION['provider'] = $utente['provider_email'];

            header("Location: home.php");
            exit;
        } else {
            $errore = "Email non registrata nell’elenco utenti autorizzati.";
        }
    }
}
?>
