<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';
require '../utils/provider_utils.php'; // contiene getSMTPHost() e getSMTPPort()

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    // 1. Verifica credenziali della casella email tramite SMTP
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = getSMTPHost($email);
        $mail->SMTPAuth = true;
        $mail->Username = $email;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = getSMTPPort($email);
        $mail->smtpConnect(); // non invia, ma tenta connessione

        // 2. Se SMTP è valido, cerca nel DB utenti
        $db = new SQLite3('../data/gestione_utenti.db');
        $stmt = $db->prepare("SELECT * FROM utenti WHERE email = :email");
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute();
        $utente = $result->fetchArray(SQLITE3_ASSOC);

        if ($utente) {
            // Autenticazione riuscita, salviamo i dati nella sessione
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            $_SESSION['nome'] = $utente['nome'];
            $_SESSION['cognome'] = $utente['cognome'];
            $_SESSION['sigla'] = $utente['sigla'];
            $_SESSION['telefono'] = $utente['telefono'];
            $_SESSION['organizzazione'] = $utente['organizzazione'];
            $_SESSION['ruolo'] = $utente['ruolo'];
            $_SESSION['provider'] = $utente['provider_email'];

            header("Location: ../index.php");
            exit;
        } else {
            $errore = "L'indirizzo email non è autorizzato all'accesso.";
        }

    } catch (Exception $e) {
        $errore = "Credenziali email non valide o casella non raggiungibile.";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
  <main class="conversion-container">
    <h2>🔐 Login Utente</h2>
    <p style="font-size: 14px; font-style: italic; color: gray;">
      Inserisci le credenziali della tua casella di posta elettronica aziendale.
    </p>
    <form method="POST">
      <label for="email">Email:</label>
      <input type="email" name="email" required>

      <label for="password">Password della casella di posta:</label>
      <input type="password" name="password" required>

      <button type="submit">Accedi</button>
    </form>
    <?php if (!empty($errore)) echo "<p style='color:red; margin-top:10px;'>$errore</p>"; ?>
  </main>
</body>
</html>
