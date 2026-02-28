<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    $tables = Schema::getAllTables();
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        // Result depends on driver, usually an object or array
        foreach ($table as $key => $value) {
            echo "- $value\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
