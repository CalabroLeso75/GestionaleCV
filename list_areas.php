<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;

$areas = DB::table('system_areas')->where('is_active', true)->get();
echo json_encode($areas, JSON_PRETTY_PRINT);
