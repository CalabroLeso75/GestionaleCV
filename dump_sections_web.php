<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$sections = \Illuminate\Support\Facades\DB::table('dashboard_sections')->get();
foreach($sections as $s) echo "ID: {$s->id} | Title: {$s->title} | Route: {$s->route} | Active: {$s->is_active}\n";
