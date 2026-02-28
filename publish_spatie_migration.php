<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;

require __DIR__.'/laravel/vendor/autoload.php';

$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

echo "<h1>Pubblicazione e Esecuzione Migrazione Spatie</h1>";

// 1. Locate Stub
$stubPath = __DIR__ . '/laravel/vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub';
$targetPath = __DIR__ . '/laravel/database/migrations/' . date('Y_m_d_His') . '_create_permission_tables.php';

if (!file_exists($stubPath)) {
    die("<h2 style='color:red'>❌ File stub non trovato in: $stubPath</h2>");
}

// 2. Copy File
if (copy($stubPath, $targetPath)) {
    echo "<p>✅ File di migrazione copiato in: $targetPath</p>";
} else {
    die("<h2 style='color:red'>❌ Impossibile copiare il file di migrazione.</h2>");
}

// 3. Run Migrate
try {
    echo "<h3>Lancio Artisan Migrate...</h3>";
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    echo "<h2 style='color:green'>✅ Tabelle Spatie Create!</h2>";
    echo "<p>Ora puoi riprovare a lanciare <a href='run_seeders.php'>run_seeders.php</a>.</p>";
} catch (\Exception $e) {
    echo "<h2 style='color:red'>❌ Errore Migrazione: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
