<?php
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['email'])) {
    $loginPath = (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? 'login.php' : 'pages/login.php';
    header("Location: $loginPath");
    exit();
}

// Determina il percorso corretto per i link (logout, profilo)
$isInPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$profilePath = $isInPages ? 'profile.php' : 'pages/profile.php';
$logoutPath = $isInPages ? 'logout.php' : 'pages/logout.php';

// Percorso corretto per il CSS
$stylePath = $isInPages ? '../css/style.css' : 'css/style.css';
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Strumenti DOS</title>
  <link rel="stylesheet" href="<?php echo $stylePath; ?>">
</head>
<body>
<div style="text-align: right; padding: 10px; font-size: 1.1em;">
    👋 Benvenuto, 
    <a href="<?php echo $profilePath; ?>" style="text-decoration: none; color: #2196f3; font-weight: bold;">
        <?php echo $_SESSION['nome'] . ' ' . $_SESSION['cognome']; ?>
    </a> |
    <a href="<?php echo $logoutPath; ?>" style="color:red; font-weight: bold;">Esci</a>
</div>
