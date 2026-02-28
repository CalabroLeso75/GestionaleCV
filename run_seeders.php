<?php

use Illuminate\Contracts\Console\Kernel;
use Database\Seeders\RoleSeeder;
use Database\Seeders\AdminUserSeeder;
use Spatie\Permission\PermissionServiceProvider;

require __DIR__.'/laravel/vendor/autoload.php';

$app = require_once __DIR__.'/laravel/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Force register provider manually if auto-discovery fails in this context
$app->register(PermissionServiceProvider::class);

echo "<h1>Esecuzione Seeder (Popolamento Ruoli e Admin - Tentativo 2)</h1>";

try {
    // Clear cache files physically
    $cachePath = __DIR__ . '/laravel/bootstrap/cache';
    array_map('unlink', glob("$cachePath/*.php"));
    echo "<p>Cache config/services pulita.</p>";

    echo "<h3>1. Creazione Ruoli...</h3>";
    $roleSeeder = new RoleSeeder();
    $roleSeeder->run();
    echo "<p style='color:green'>✅ Ruoli creati!</p>";
    
    echo "<h3>2. Creazione Utente Admin...</h3>";
    $adminSeeder = new AdminUserSeeder();
    $adminSeeder->run();
    echo "<p style='color:green'>✅ Utente Admin creato (admin@calabriaverde.it / password)</p>";
    
    echo "<h2>🎉 TUTTO FATTO!</h2>";
    echo "<p><a href='/GestionaleCV/laravel/public/login'>Vai al Login</a></p>"; 
    
} catch (\Exception $e) {
    echo "<h2 style='color:red'>❌ Errore: " . $e->getMessage() . "</h2>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
