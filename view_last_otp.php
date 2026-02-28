<?php

$logFile = __DIR__ . '/storage/logs/laravel.log';

echo "<h1>Ultimo OTP Generato</h1>";

if (!file_exists($logFile)) {
    die("File di log non trovato.");
}

$content = file_get_contents($logFile);
// Find all matches for "OTP for"
preg_match_all('/OTP for (.*?): (\d{8})/', $content, $matches);

if (!empty($matches[0])) {
    $lastIndex = count($matches[0]) - 1;
    $email = $matches[1][$lastIndex];
    $code = $matches[2][$lastIndex];
    
    echo "<div style='font-size: 24px; padding: 20px; border: 2px solid green; display: inline-block;'>";
    echo "Email: <strong>$email</strong><br>";
    echo "Codice: <strong style='color: red; font-size: 40px;'>$code</strong>";
    echo "</div>";
    
    echo "<p><small>Trovato nel log: " . $matches[0][$lastIndex] . "</small></p>";
} else {
    echo "<p>Nessun OTP trovato nel log recente.</p>";
}

echo "<hr>";
echo "<p><a href='/GestionaleCV/register'>Torna alla Registrazione</a> | <a href='/GestionaleCV/verify-otp'>Vai a Verifica OTP</a></p>";
