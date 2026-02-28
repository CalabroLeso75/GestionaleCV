<?php
$url = 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php';
$dest = __DIR__ . '/adminer.php';

echo "Downloading Adminer from $url...<br>";

// Try file_get_contents first (simple)
$content = @file_get_contents($url);

if ($content === false) {
    echo "file_get_contents failed, trying cURL...<br>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Local dev often lacks certs
    $content = curl_exec($ch);
    curl_close($ch);
}

if ($content && strlen($content) > 10000) {
    file_put_contents($dest, $content);
    echo "<h1>✅ Adminer Installato con Successo!</h1>";
    echo "<p>File salvato in: $dest</p>";
    echo "<p><a href='adminer.php'>Clicca qui per aprire Adminer</a></p>";
} else {
    echo "<h1>❌ Errore Download</h1>";
    echo "<p>Impossibile scaricare il file. Verifica la connessione internet.</p>";
}
