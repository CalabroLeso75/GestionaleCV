<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "CHECKING STATUS\n";
echo "Vehicles Table: " . (Schema::hasTable('vehicles') ? "EXISTS" : "MISSING") . "\n";
echo "Vehicle Logs Table: " . (Schema::hasTable('vehicle_logs') ? "EXISTS" : "MISSING") . "\n";

$area = DB::table('system_areas')->where('slug', 'autoparco')->first();
if ($area) {
    echo "Autoparco Area: FOUND (ID: {$area->id}, Active: {$area->is_active})\n";
} else {
    echo "Autoparco Area: NOT FOUND\n";
}

$userArea = DB::table('user_area_roles')->where('user_id', 1)->where('area', 'autoparco')->first();
if ($userArea) {
    echo "User 1 Permission: FOUND (Role: {$userArea->role})\n";
} else {
    echo "User 1 Permission: NOT FOUND\n";
}
