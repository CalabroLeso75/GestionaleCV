<?php
use App\Models\Organization;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create test organizations
$org1 = Organization::updateOrCreate(
    ['tax_code' => '99999999991'],
    [
        'name' => 'Associazione Volontari AIB - Test',
        'type' => 'private',
        'is_aib' => true
    ]
);

$org2 = Organization::updateOrCreate(
    ['tax_code' => '99999999992'],
    [
        'name' => 'Ditta Privata - Edilizia',
        'type' => 'private',
        'is_aib' => false
    ]
);

echo "Organizations created/updated:\n";
echo "- {$org1->name} (is_aib: " . ($org1->is_aib ? 'YES' : 'NO') . ")\n";
echo "- {$org2->name} (is_aib: " . ($org2->is_aib ? 'YES' : 'NO') . ")\n";
