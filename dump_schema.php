<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$schema = [];

foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    $columns = DB::select("DESCRIBE $tableName");
    $schema[$tableName] = $columns;
}

echo json_encode($schema, JSON_PRETTY_PRINT);
