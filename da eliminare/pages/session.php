<?php
session_start();

// Se non è loggato
if (!isset($_SESSION['email'])) {
    // Verifica da dove è stato incluso il file
    $backtrace = debug_backtrace();
    $callerFile = isset($backtrace[0]['file']) ? $backtrace[0]['file'] : '';
    
    // Se la pagina che include è nella root, il login è in /pages/login.php
    if (strpos($callerFile, '/pages/') === false) {
        header("Location: pages/login.php");
    } else {
        header("Location: login.php");
    }
    exit();
}
?>

