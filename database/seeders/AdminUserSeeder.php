<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin exists
        $admin = User::where('email', 'admin@calabriaverde.it')->first();

        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@calabriaverde.it',
                'password' => Hash::make('password'),
            ]);
            
            // Assign super-admin role
            $admin->assignRole('super-admin');
        }
    }
}
