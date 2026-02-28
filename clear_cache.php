<?php

/**
 * Script per svuotare le cache di configurazione di Laravel.
 * Utile dopo aver modificato il file .env.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h1>Pulizia Cache Configurazione</h1>";

try {
    // Clear config cache
    $configCachePath = base_path('bootstrap/cache/config.php');
    if (file_exists($configCachePath)) {
        unlink($configCachePath);
        echo "<p>✅ Cache configurazione eliminata.</p>";
    } else {
        echo "<p>ℹ️ Nessuna cache di configurazione trovata (ok, non era in cache).</p>";
    }

    // Clear route cache
    $routeCachePath = base_path('bootstrap/cache/routes-v7.php');
    if (file_exists($routeCachePath)) {
        unlink($routeCachePath);
        echo "<p>✅ Cache rotte eliminata.</p>";
    }

    // Clear views cache
    $viewsCachePath = storage_path('framework/views');
    if (is_dir($viewsCachePath)) {
        $files = glob($viewsCachePath . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "<p>✅ Cache viste eliminata ($count file).</p>";
    }

    // Verify current mail config
    echo "<h2>Configurazione Mail Attuale (.env)</h2>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Chiave</th><th>Valore</th></tr>";
    $mailKeys = ['MAIL_MAILER', 'MAIL_SCHEME', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME'];
    foreach ($mailKeys as $key) {
        echo "<tr><td><strong>$key</strong></td><td>" . env($key, '(vuoto)') . "</td></tr>";
    }
    echo "<tr><td><strong>MAIL_PASSWORD</strong></td><td>" . (env('MAIL_PASSWORD') ? '********' : '(vuoto)') . "</td></tr>";
    echo "</table>";

    echo "<p style='color:green; font-weight:bold; margin-top:20px'>✅ Cache pulite! Ricarica la pagina SMTP per riprovare.</p>";
    echo "<p><a href='/GestionaleCV/admin/smtp' class='btn btn-primary'>Vai alle Impostazioni SMTP</a></p>";

} catch (\Exception $e) {
    echo "<p style='color:red'>Errore: " . $e->getMessage() . "</p>";
}
