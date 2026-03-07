<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sections = \Illuminate\Support\Facades\DB::table('dashboard_sections')->get();
echo "--- DASHBOARD SECTIONS ---\n";
foreach($sections as $s) {
    echo "ID: {$s->id} | Route: {$s->route} | Title: {$s->title} | Active: {$s->is_active} | Role: {$s->required_role} | Area: {$s->required_area}\n";
}

$users = \App\Models\User::take(5)->get();
echo "\n--- USERS ---\n";
foreach($users as $u) {
    echo "User: {$u->email} | Auth Role: " . $u->roles->pluck('name')->implode(',') . "\n";
}
