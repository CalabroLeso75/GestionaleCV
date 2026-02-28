<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "--- Sincronizzazione Aree Protezione Civile ---\n";

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

$count = 0;
foreach ($areas as $a) {
    // We use slug as the unique identifier
    $DB->table('system_areas')->updateOrInsert(
        ['slug' => $a[1]],
        [
            'name' => $a[0],
            'description' => $a[2],
            'is_active' => 1,
            'updated_at' => now(),
        ]
    );
    echo "Check: {$a[0]} ({$a[1]})\n";
    $count++;
}

// Special check: ensure dashboard_sections is also active
$DB->table('dashboard_sections')
    ->where('title', 'Gestione delle emergenze di Protezione Civile')
    ->update(['is_active' => 1]);

echo "\nSincronizzazione completata: $count aree verificate.\n";
echo "Pannello Protezione Civile forzato come ATTIVO.\n";
