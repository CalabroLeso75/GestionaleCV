<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$section = DB::table('dashboard_sections')->where('slug', 'autoparco')->first();

if ($section) {
    DB::table('dashboard_sections')->where('slug', 'autoparco')->update([
        'title' => 'Parco Macchine aziendale',
        'route' => '/autoparco',
        'icon' => '🚗',
        'color' => '#3498db',
        'description' => 'Gestione mezzi, scadenze, km e passaggi di consegna.',
        'required_area' => 'Autoparco',
        'sort_order' => 3,
        'is_active' => true
    ]);
    echo "Updated Autoparco section.\n";
} else {
    DB::table('dashboard_sections')->insert([
        'title' => 'Parco Macchine aziendale',
        'slug' => 'autoparco',
        'route' => '/autoparco',
        'icon' => '🚗',
        'color' => '#3498db',
        'description' => 'Gestione mezzi, scadenze, km e passaggi di consegna.',
        'required_area' => 'Autoparco',
        'sort_order' => 3,
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo "Inserted Autoparco section.\n";
}
