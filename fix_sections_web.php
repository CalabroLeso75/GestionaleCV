<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "--- Forcing PC and HR Sections in dashboard_sections ---\n";

$sections = [
    [
        'title' => 'Risorse Umane',
        'description' => 'Anagrafica, fascicoli, codice fiscale',
        'icon' => '👥',
        'route' => '/hr',
        'color' => '#2e7d32',
        'is_active' => 1,
        'sort_order' => 10,
    ],
    [
        'title' => 'Gestione delle emergenze di Protezione Civile',
        'description' => 'SOUP, COP, AIB e gestione emergenze territoriali',
        'icon' => '🚨',
        'route' => '/pc',
        'color' => '#d32f2f',
        'is_active' => 1,
        'sort_order' => 20,
    ]
];

foreach ($sections as $s) {
    $existing = $DB->table('dashboard_sections')->where('title', $s['title'])->first();
    if ($existing) {
        echo "Updating: {$s['title']}\n";
        $DB->table('dashboard_sections')->where('id', $existing->id)->update($s);
    } else {
        echo "Inserting: {$s['title']}\n";
        $DB->table('dashboard_sections')->insert($s);
    }
}

// Cleanup: remove any other duplicate by route
$all = $DB->table('dashboard_sections')->get();
$seen = [];
foreach ($all as $curr) {
    if (isset($seen[$curr->route])) {
        echo "Deleting duplicate route: {$curr->route} (ID: {$curr->id})\n";
        $DB->table('dashboard_sections')->where('id', $curr->id)->delete();
    } else {
        $seen[$curr->route] = true;
    }
}

echo "\nDone. Current sections:\n";
$final = $DB->table('dashboard_sections')->get();
foreach ($final as $f) {
    echo "- [{$f->id}] {$f->title} ({$f->route})\n";
}
