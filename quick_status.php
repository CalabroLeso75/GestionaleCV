<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- DATABASE STATUS ---\n";
echo "Vehicles count: " . (Schema::hasTable('vehicles') ? DB::table('vehicles')->count() : "TABLE MISSING") . "\n";
echo "Dashboard Sections count: " . (Schema::hasTable('dashboard_sections') ? DB::table('dashboard_sections')->count() : "TABLE MISSING") . "\n";

if (Schema::hasTable('dashboard_sections')) {
    $sections = DB::table('dashboard_sections')->get();
    foreach ($sections as $s) {
        echo "- Section: {$s->title} (Route: {$s->route}, Active: {$s->is_active})\n";
    }
}

$user = DB::table('users')->where('email', 'raffaele.cusano@calabriaverde.eu')->first();
if ($user) {
    echo "User found: {$user->email} (ID: {$user->id})\n";
    $roles = DB::table('user_area_roles')->where('user_id', $user->id)->get();
    echo "User Area Roles count: " . $roles->count() . "\n";
    foreach ($roles as $r) {
        echo "  - Area: {$r->area} (Role: {$r->role})\n";
    }
}
echo "--- END ---\n";
