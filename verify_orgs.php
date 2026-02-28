<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;

// Ensure test organizations exist
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

$orgs = Organization::where('tax_code', 'LIKE', '9999%')->get();
if ($orgs->isEmpty()) {
    echo "NO ORGS FOUND - SOMETHING IS WRONG\n";
} else {
    foreach ($orgs as $o) {
        echo "ID: {$o->id}, Name: {$o->name}, AIB: " . ($o->is_aib ? '1' : '0') . "\n";
    }
}
echo "Verification complete.\n";
