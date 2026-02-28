<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;

$tables = ['vehicles', 'vehicle_logs', 'incidents', 'aib_stations', 'aib_teams'];
foreach ($tables as $t) {
    echo "Table '$t': " . (Schema::hasTable($t) ? "EXISTS" : "MISSING") . "\n";
}
