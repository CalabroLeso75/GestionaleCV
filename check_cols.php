<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Show column types for internal_employees
$columns = $DB->select("SHOW COLUMNS FROM internal_employees");
echo "=== internal_employees columns ===\n";
foreach ($columns as $col) {
    echo "  {$col->Field}: {$col->Type} | Null:{$col->Null} | Default:{$col->Default}\n";
}

// Check SQL mode
$mode = $DB->select("SELECT @@SESSION.sql_mode as mode");
echo "\nSQL Mode: {$mode[0]->mode}\n";
