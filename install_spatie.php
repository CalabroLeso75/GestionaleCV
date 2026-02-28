<?php
set_time_limit(300);
ini_set('memory_limit', '1G');

echo "<h1>Installazione Spatie Permission</h1>";

$composerUrl = 'https://getcomposer.org/installer';
$composerPhar = __DIR__ . '/composer.phar';

// 1. Download Composer if needed
if (!file_exists($composerPhar)) {
    echo "<p>Download di composer.phar...</p>";
    copy('https://getcomposer.org/composer.phar', $composerPhar);
}

if (file_exists($composerPhar)) {
    echo "<p>✅ composer.phar presente.</p>";
} else {
    die("<p>❌ Impossibile scaricare composer.phar</p>");
}

// 2. Run Require Command
echo "<h3>Esecuzione: composer require spatie/laravel-permission</h3>";
echo "<pre>";

// We need to run this in the 'laravel' subdirectory
$cwd = __DIR__ . '/laravel';
$cmd = "php \"$composerPhar\" require spatie/laravel-permission --working-dir=\"$cwd\" 2>&1";

$output = [];
$return_var = 0;
exec($cmd, $output, $return_var);

foreach ($output as $line) {
    echo htmlspecialchars($line) . "\n";
}

echo "</pre>";

if ($return_var === 0) {
    echo "<h2 style='color:green'>✅ Installazione Completata!</h2>";
    echo "<p>Ora puoi riprovare a lanciare <a href='run_seeders.php'>run_seeders.php</a>.</p>";
} else {
    echo "<h2 style='color:red'>❌ Errore Installazione ($return_var)</h2>";
}
