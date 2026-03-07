<?php
// File da caricare nella cartella "public" (o document root) dell'hosting

$output = [];

// 1. Eliminazione manuale dei file di cache di Laravel
// Se hai copiato la cartella bootstrap/cache da XAMPP, i percorsi assoluti "C:\xampp..." causeranno errori 500.
$pathsToClear = [
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/services.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
];

foreach ($pathsToClear as $path) {
    if (file_exists($path)) {
        unlink($path);
        $output[] = "Eliminato file di cache errato: " . basename($path);
    }
}

// Pulizia cache delle viste (Blade)
$viewsPath = __DIR__ . '/../storage/framework/views/';
if (is_dir($viewsPath)) {
    $files = glob($viewsPath . '*');
    $viewCount = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            $viewCount++;
        }
    }
    $output[] = "Pulita la cache delle viste (eliminate $viewCount viste compilate).";
}

// 2. Creazione dello Storage Link (php artisan storage:link)
// Il trasferimento FTP/SFTP semplice non ricrea i collegamenti simbolici.
$target = realpath(__DIR__ . '/../storage/app/public');
$link = __DIR__ . '/storage';

if ($target) {
    if (file_exists($link)) {
        if (is_link($link)) {
            unlink($link);
            $output[] = "Vecchio symlink storage rimosso.";
        } else {
            $output[] = "Attenzione: Esiste già una cartella 'storage' in public che non è un link. Rimuovila manualmente se vuoi ricreare il symlink.";
        }
    }

    if (!file_exists($link)) {
        if (@symlink($target, $link)) {
            $output[] = "Creato nuovo link simbolico (storage:link) con successo.";
        } else {
            $output[] = "Errore: Impossibile creare il link simbolico. Il tuo hosting potrebbe aver disabilitato la funzione symlink().";
        }
    }
} else {
    $output[] = "La cartella target per lo storage non esiste (" . __DIR__ . "/../storage/app/public).";
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Setup Hosting Laravel</title>
    <style>body { font-family: sans-serif; padding: 20px; }</style>
</head>
<body>
    <h1>Setup Iniziale Hosting Completato</h1>
    <ul>
        <?php foreach ($output as $msg): ?>
            <li><?php echo htmlspecialchars($msg); ?></li>
        <?php endforeach; ?>
    </ul>

    <p style="color: red; font-weight: bold;">ATTENZIONE: Ora elimina assolutamente questo file (setup_hosting.php) dal server per evitare problemi di sicurezza!</p>
    <p>Ricordati inoltre di verificare il file <strong>.env</strong> sull'hosting assicurandoti che:</p>
    <ul>
        <li>I dati del database (DB_DATABASE, DB_USERNAME, DB_PASSWORD) siano quelli forniti dall'hosting.</li>
        <li><code>APP_ENV=production</code></li>
        <li><code>APP_DEBUG=false</code></li>
        <li><code>APP_URL=https://iltuosito.it</code></li>
    </ul>
</body>
</html>
