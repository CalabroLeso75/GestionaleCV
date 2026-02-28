<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "--- Aggiornamento Sezioni Dashboard ---\n";

// 1. Add Protezione Civile Section
$pcSection = [
    'title' => 'Gestione delle emergenze di Protezione Civile',
    'description' => 'SOUP, COP, AIB e gestione emergenze territoriali',
    'icon' => '🚨',
    'route' => '/pc',
    'color' => '#d32f2f',
    'is_active' => 1,
    'sort_order' => 20,
];

$existing = $DB->table('dashboard_sections')->where('title', $pcSection['title'])->first();
if ($existing) {
    echo "Sezione PC già esistente. Aggiornamento in corso...\n";
    $DB->table('dashboard_sections')->where('id', $existing->id)->update($pcSection);
} else {
    echo "Inserimento nuova sezione PC...\n";
    $DB->table('dashboard_sections')->insert($pcSection);
}

echo "\n--- Aggiornamento Aree Sistema (Sotto-sezioni) ---\n";

// 2. Add System Areas
$areas = [
    ['Sala Operativa Unificata Permanente (SOUP)', 'soup', 'Sala operativa centrale'],
    ['Centro Operativo Provinciale di Cosenza (COPCS)', 'cop-cosenza', 'COP Cosenza'],
    ['Centro Operativo Provinciale di Catanzaro (COPCZ)', 'cop-catanzaro', 'COP Catanzaro'],
    ['Centro Operativo Provinciale di Crotone (COPKR)', 'cop-crotone', 'COP Crotone'],
    ['Centro Operativo Provinciale di Vibo Valentia (COPVV)', 'cop-vibo-valentia', 'COP Vibo Valentia'],
    ['Centro Operativo Provinciale di Reggio Calabria (COPRC)', 'cop-reggio-calabria', 'COP Reggio Calabria'],
    ['Antincendio Boschivo', 'antincendio-boschivo', 'Prevenzione e lotta incendi boschivi'],
    ['Gestione delle Emergenze di Protezione Civile', 'emergenze-pc', 'Coordinamento emergenze'],
    ['Gestione dei Mezzi Aerei Regionali', 'mezzi-aerei', 'Flotta aerea regionale'],
    ['Gestione delle Squadre AIB e Supporto PC', 'squadre-aib-pc', 'Coordinamento squadre terra'],
    ['Gestione dei Mezzi di terra', 'mezzi-terra', 'Parco mezzi terrestre'],
    ['Gestione Utenze Telefoni e indirizzi di posta aziendali', 'utenze-aziendali', 'Gestione comunicazioni'],
    ['Gestione turnazioni personale AIB', 'turnazioni-aib', 'Turni e reperibilità'],
];

$sortOrder = 100; // Start from 100 for PC areas
foreach ($areas as $a) {
    $DB->table('system_areas')->updateOrInsert(
        ['slug' => $a[1]],
        [
            'name' => $a[0],
            'description' => $a[2],
            'sort_order' => $sortOrder++,
            'is_active' => 1,
            'updated_at' => now(),
        ]
    );
    echo "  ✓ Area: {$a[0]} ({$a[1]})\n";
}

echo "\nOperazione completata con successo!\n";
