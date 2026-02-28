<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$out = "DATABASE DIAGNOSTICS\n";
$out .= "====================\n\n";

$out .= "SCHEMA: vehicles table\n";
foreach (DB::select("SHOW COLUMNS FROM vehicles") as $col) {
    $out .= sprintf("%-25s | %-15s | %-5s\n", $col->Field, $col->Type, $col->Null);
}

$out .= "\nDATA: First Vehicle\n";
$v = Vehicle::first();
if ($v) {
    foreach ($v->toArray() as $key => $val) {
        $out .= sprintf("%-25s: %s\n", $key, is_array($val) ? json_encode($val) : $val);
    }
} else {
    $out .= "No vehicles found.\n";
}

file_put_contents('db_dump.txt', $out);
echo "Diagnostics written to db_dump.txt\n";
