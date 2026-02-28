<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Insert HR dashboard section
$exists = $DB->table('dashboard_sections')->where('title', 'Risorse Umane')->exists();
if ($exists) {
    echo "Tessera 'Risorse Umane' esiste già.\n";
} else {
    $DB->table('dashboard_sections')->insert([
        'title' => 'Risorse Umane',
        'description' => 'Gestione anagrafica, fascicoli personale, codice fiscale',
        'icon' => '👥',
        'route' => '/hr',
        'color' => '#2e7d32',
        'required_role' => null,
        'is_active' => true,
        'sort_order' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Tessera 'Risorse Umane' inserita!\n";
}

echo "\nSezioni dashboard:\n";
$secs = $DB->table('dashboard_sections')->orderBy('sort_order')->get();
foreach ($secs as $s) echo "  {$s->icon} {$s->title} → {$s->route}\n";
