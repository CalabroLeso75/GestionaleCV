<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        Role::create(['name' => 'super-admin']); // Full access
        Role::create(['name' => 'admin']);       // General Manager
        Role::create(['name' => 'hr-manager']);  // HR Module Access
        Role::create(['name' => 'employee']);    // Basic Access
        
        // Example permissions (future use)
        // Permission::create(['name' => 'manage employees']);
        // $role = Role::findByName('hr-manager');
        // $role->givePermissionTo('manage employees');
    }
}
