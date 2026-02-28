<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/laravel/vendor/autoload.php';

$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

echo "<h1>Esecuzione Migrazioni (Tentativo 4)</h1>";

// Manually verify tables state using native method
$tables = Schema::getTables(); // Laravel 11/12 native method
echo "<h3>Tabelle Esistenti:</h3><ul>";
foreach($tables as $t) {
    if (is_array($t)) {
        $name = $t['name'] ?? 'unknown';
    } elseif (is_object($t)) {
        $name = $t->name ?? 'unknown';
    } else {
        $name = (string)$t;
    }
    echo "<li>$name</li>";
}
echo "</ul>";

try {
    // Force delete cached migration file (if opcache or similar is holding it)
    $file = __DIR__ . '/laravel/database/migrations/2026_02_15_225000_create_employees_table.php';
    if (file_exists($file)) {
        unlink($file);
        echo "<p>Cancellato file migrazione obsoleto: $file</p>";
    }
    
    // Check if renaming is needed manually if migration fails
    // Native rename doesn't need doctrine/dbal on modern MySQL 8+
    
    echo "<h3>Lancio Artisan Migrate...</h3>";
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "<pre>" . Illuminate\Support\Facades\Artisan::output() . "</pre>";
    echo "<h2 style='color:green'>✅ Migrazioni completate!</h2>";
} catch (\Exception $e) {
    echo "<h2 style='color:red'>❌ Errore: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
