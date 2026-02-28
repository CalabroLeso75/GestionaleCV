<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;

$organizations = [
    [
        'name' => 'Nessuna Organizzazione',
        'type' => 'public',
        'tax_code' => '00000000000',
        'is_aib' => false
    ],
    [
        'name' => 'Organizzazione di volontariato (PC)',
        'type' => 'private',
        'tax_code' => '90000000001',
        'is_aib' => false
    ],
    [
        'name' => 'Associazione di volontaria AIB',
        'type' => 'private',
        'tax_code' => '90000000002',
        'is_aib' => true
    ],
    [
        'name' => 'Vigili del Fuoco',
        'type' => 'public',
        'tax_code' => '90000000003',
        'is_aib' => false
    ],
    [
        'name' => 'Carabinieri Forestali',
        'type' => 'public',
        'tax_code' => '90000000004',
        'is_aib' => false
    ],
];

echo "Seeding organizations...\n";

foreach ($organizations as $orgData) {
    $org = Organization::updateOrCreate(
        ['name' => $orgData['name']],
        $orgData
    );
    echo "- Created/Updated: {$org->name} (ID: {$org->id})\n";
}

echo "Seeding complete.\n";
