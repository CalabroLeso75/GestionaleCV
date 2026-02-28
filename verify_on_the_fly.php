<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ExternalEmployee;
use App\Models\Organization;

echo "Testing On-The-Fly Organization Creation...\n";

// Clear existing test data
ExternalEmployee::where('tax_code', 'GIALUI90C03L219Y')->delete();
Organization::where('name', 'Nuova Ditta Test')->delete();

// Simulated request data
$data = [
    'last_name' => 'Gialli',
    'first_name' => 'Luigi',
    'tax_code' => 'GIALUI90C03L219Y',
    'birth_date' => '1990-03-03',
    'organization_id' => 'new',
    'job_title' => 'Ingegnere',
    'is_aib' => '0',
    'new_org_name' => 'Nuova Ditta Test',
    'new_org_type' => 'private',
    'new_org_tax_code' => '12345678901',
    'new_org_is_aib' => '1' // The new org is AIB
];

// Replicate controller logic
try {
    if ($data['organization_id'] === 'new') {
        $org = Organization::create([
            'name' => $data['new_org_name'],
            'type' => $data['new_org_type'],
            'tax_code' => $data['new_org_tax_code'],
            'is_aib' => filter_var($data['new_org_is_aib'], FILTER_VALIDATE_BOOLEAN),
        ]);
        $data['organization_id'] = $org->id;
        echo "Created Organization: {$org->name} (ID: {$org->id}, AIB: " . ($org->is_aib ? '1' : '0') . ")\n";
        
        if ($org->is_aib) {
            $data['is_aib'] = true;
        }
    }

    $emp = ExternalEmployee::create($data);
    echo "Created Employee: {$emp->first_name} {$emp->last_name} (ID: {$emp->id}, AIB: " . ($emp->is_aib ? '1' : '0') . ")\n";
    echo "Linked to Org ID: {$emp->organization_id} (" . ($emp->organization_id == $org->id ? 'MATCH' : 'FAIL') . ")\n";
    echo "AIB auto-set check: " . ($emp->is_aib ? 'PASS' : 'FAIL') . " (Expected: PASS because org is AIB)\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nVerification complete.\n";
