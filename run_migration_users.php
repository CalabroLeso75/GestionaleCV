<?php

use Illuminate\Support\Facades\Artisan;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h1>Esecuzione Migrazione Users Table</h1>";

try {
    Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Artisan::output() . "</pre>";
    echo "<h2 style='color:green'>✅ Migrazione completata!</h2>";
} catch (\Exception $e) {
    echo "<h2 style='color:red'>❌ Errore: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
