<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$out = "DIAGNOSTIC REPORT\n";
$out .= "Date: " . date('Y-m-d H:i:s') . "\n";
$out .= "Vehicles Table: " . (Schema::hasTable('vehicles') ? "EXISTS" : "MISSING") . "\n";
$out .= "Vehicle Logs Table: " . (Schema::hasTable('vehicle_logs') ? "EXISTS" : "MISSING") . "\n";

$area = DB::table('system_areas')->where('slug', 'autoparco')->first();
if ($area) {
    $out .= "Autoparco Area: FOUND (ID: {$area->id}, Active: {$area->is_active}, Name: {$area->name})\n";
} else {
    $out .= "Autoparco Area: NOT FOUND\n";
}

$user = DB::table('users')->where('id', 1)->first();
if ($user) {
    $out .= "User 1: {$user->email} (Status: {$user->status}, Type: {$user->type})\n";
}

$userArea = DB::table('user_area_roles')->where('user_id', 1)->first(); // Just see any role
if ($userArea) {
    $out .= "Some User Role Found: Area={$userArea->area}, Role={$userArea->role}\n";
}

// Check if Autoparco is in pcAreaSlugs list in ProtezioneCivileController (manually by reading file later)

file_put_contents(__DIR__.'/diag_result.txt', $out);
echo "Diagnostic done.\n";
