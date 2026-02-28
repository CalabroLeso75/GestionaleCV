<?php
// Scratch script to verify Autoparco RBAC and trigger force-fix-db logic
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

echo "--- VERIFICATION START ---\n";

// 1. Run the Roles/Permissions logic manually
try {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = [
        'vehicle.full_edit',
        'vehicle.limited_edit',
        'vehicle.assign',
        'vehicle.view_logs'
    ];
    foreach ($permissions as $p) {
        Permission::findOrCreate($p);
    }

    $roleAdmin = Role::findOrCreate('amministratore di sistema');
    $roleAdmin->givePermissionTo($permissions);

    $roleManager = Role::findOrCreate('responsabile parco macchine');
    $roleManager->syncPermissions($permissions);

    $roleOperator = Role::findOrCreate('operatore parco macchine');
    $roleOperator->syncPermissions(['vehicle.limited_edit', 'vehicle.assign', 'vehicle.view_logs']);

    echo "Roles and Permissions configured successfully.\n";
} catch (\Exception $e) {
    echo "ERROR in Roles setup: " . $e->getMessage() . "\n";
}

// 2. Check if roles have correct permissions
$manager = Role::findByName('responsabile parco macchine');
echo "Manager has full_edit: " . ($manager->hasPermissionTo('vehicle.full_edit') ? "YES" : "NO") . "\n";

$operator = Role::findByName('operatore parco macchine');
echo "Operator has full_edit: " . ($operator->hasPermissionTo('vehicle.full_edit') ? "YES" : "NO") . "\n";
echo "Operator has assign: " . ($operator->hasPermissionTo('vehicle.assign') ? "YES" : "NO") . "\n";

echo "--- VERIFICATION END ---\n";
