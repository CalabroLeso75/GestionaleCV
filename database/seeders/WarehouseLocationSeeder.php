<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WarehouseLocation;

class WarehouseLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sede Centrale
        $sedeCentrale = WarehouseLocation::create([
            'name' => 'Sede Centrale',
            'type' => 'sede_centrale',
            'parent_id' => null,
        ]);

        $hubCentrale = WarehouseLocation::create([
            'name' => 'HUB Centrale',
            'type' => 'hub_centrale',
            'parent_id' => $sedeCentrale->id,
        ]);

        $magazzinoCentrale = WarehouseLocation::create([
            'name' => 'Magazzino Centrale',
            'type' => 'magazzino_centrale',
            'parent_id' => $sedeCentrale->id,
        ]);

        // 2. Distretti 1-11
        for ($i = 1; $i <= 11; $i++) {
            $distretto = WarehouseLocation::create([
                'name' => 'Distretto ' . $i,
                'type' => 'distretto',
                'parent_id' => $sedeCentrale->id, // Tutti figurano sotto Sede Centrale!
            ]);

            // HUB del distretto
            WarehouseLocation::create([
                'name' => 'HUB Distretto ' . $i,
                'type' => 'hub_distretto',
                'parent_id' => $distretto->id,
            ]);

            // Magazzino scorte del distretto
            WarehouseLocation::create([
                'name' => 'Magazzino Distretto ' . $i,
                'type' => 'magazzino_distretto',
                'parent_id' => $distretto->id,
            ]);
        }
    }
}
