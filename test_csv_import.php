<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Incident;
use App\Models\Location\City;
use Carbon\Carbon;

$path = __DIR__ . '/sample_pc2.csv';
$data = array_map(function($line) {
    return str_getcsv($line, ';');
}, file($path));

$header = array_shift($data);
$importedCount = 0;

echo "Starting import test...\n";

foreach ($data as $row) {
    if (count($row) < count($header)) continue;
    
    $rowData = array_combine($header, $row);
    $comuneName = $rowData['Comune'] ?? null;
    $comune = City::where('name', 'LIKE', '%' . $comuneName . '%')->first();
    
    if (!$comune) {
        echo "Comune $comuneName not found, skipping.\n";
        continue;
    }

    $incident = Incident::updateOrCreate(
        ['pcm_incidente_id' => $rowData['ID_PCM'] ?? null],
        [
            'codice_incidente' => Incident::generateCode(),
            'data_ora' => Carbon::parse($rowData['Data'] ?? now()),
            'comune_id' => $comune->id,
            'indirizzo' => $rowData['Indirizzo'] ?? null,
            'tipo_evento' => $rowData['Tipo'] ?? 'Generico',
            'priorita' => $rowData['Priorita'] ?? 'Media',
            'descrizione' => $rowData['Note'] ?? null,
            'stato' => 'Aperto',
        ]
    );
    echo "Imported: {$incident->codice_incidente} - {$incident->tipo_evento} at {$comune->name}\n";
    $importedCount++;
}

echo "Finished. Total: $importedCount\n";
