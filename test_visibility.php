<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\DashboardSection;

$fp = fopen('visibility_debug.txt', 'w');

// 1. Check all users and their roles
$users = User::all();
fwrite($fp, "USERS CHECK:\n");
foreach ($users as $u) {
    $roles = $u->getRoleNames()->toArray();
    fwrite($fp, "  [{$u->id}] {$u->name} {$u->surname} ({$u->email}) | Roles: " . implode(', ', $roles) . " | Status: {$u->status}\n");
}

// 2. Check sections in DB
fwrite($fp, "\nSECTIONS CHECK:\n");
$sections = \Illuminate\Support\Facades\DB::table('dashboard_sections')->get();
foreach ($sections as $s) {
    fwrite($fp, "  [{$s->id}] Title: {$s->title} | Active: {$s->is_active} | Role: {$s->required_role} | Area: {$s->required_area} | Route: {$s->route}\n");
}

// 3. Check visibleTo for each user
fwrite($fp, "\nVISIBILITY TEST:\n");
foreach ($users as $u) {
    $visible = DashboardSection::visibleTo($u);
    fwrite($fp, "  User {$u->id} sees " . count($visible) . " sections:\n");
    foreach ($visible as $v) {
        fwrite($fp, "    - {$v->title}\n");
    }
}

fclose($fp);
echo "SUCCESS\n";
