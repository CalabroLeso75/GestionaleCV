<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;
use App\Models\ExternalEmployee;

$keepNames = [
    'Nessuna Organizzazione',
    'Organizzazione di volontariato (PC)',
    'Associazione di volontaria AIB',
    'Vigili del Fuoco',
    'Carabinieri Forestali'
];

echo "Cleaning up organizations...\n";

// First, delete test external employees to avoid FK issues
echo "Deleting test employees...\n";
ExternalEmployee::whereIn('tax_code', ['RSSMRA80A01H501Z', 'BNCMRA90B02F205W', 'GIALUI90C03L219Y'])->delete();

// Delete organizations not in the keep list
$organizationsToDelete = Organization::whereNotIn('name', $keepNames)->get();
foreach ($organizationsToDelete as $org) {
    echo "- Deleting: {$org->name} (ID: {$org->id})\n";
    $org->delete();
}

// Ensure the 5 requested ones exist with correct settings
$orgs = [
    ['name' => 'Nessuna Organizzazione', 'type' => 'public', 'is_aib' => false],
    ['name' => 'Organizzazione di volontariato (PC)', 'type' => 'private', 'is_aib' => false],
    ['name' => 'Associazione di volontaria AIB', 'type' => 'private', 'is_aib' => true],
    ['name' => 'Vigili del Fuoco', 'type' => 'public', 'is_aib' => false],
    ['name' => 'Carabinieri Forestali', 'type' => 'public', 'is_aib' => false],
];

foreach ($orgs as $o) {
    Organization::updateOrCreate(['name' => $o['name']], $o);
    echo "- Verified: {$o['name']}\n";
}

echo "\nDone. Current organizations:\n";
foreach (Organization::all() as $o) {
    echo "  [{$o->id}] {$o->name} (AIB: " . ($o->is_aib ? 'Sì' : 'No') . ")\n";
}
