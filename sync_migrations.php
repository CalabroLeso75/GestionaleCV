<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    // Check if the advanced structures migration already actually ran
    if (Schema::hasTable('aib_team_members') && Schema::hasTable('aib_team_vehicles') && Schema::hasTable('aib_team_phones')) {
        echo "Tables for advanced structures exist.\n";
        if (!DB::table('migrations')->where('migration', '2026_02_27_122401_update_aib_team_advanced_structures')->exists()) {
            DB::table('migrations')->insert([
                'migration' => '2026_02_27_122401_update_aib_team_advanced_structures',
                'batch' => 2
            ]);
            echo "Marked update_aib_team_advanced_structures as run.\n";
        }
    } else {
        echo "Tables for advanced structures DO NOT exist, migration should run.\n";
    }

    // Check if stations structure migration already ran
    if (Schema::hasTable('aib_team_stations')) {
        echo "Table aib_team_stations exists.\n";
        if (!DB::table('migrations')->where('migration', '2026_02_27_150201_update_aib_team_stations_structure')->exists()) {
            DB::table('migrations')->insert([
                'migration' => '2026_02_27_150201_update_aib_team_stations_structure',
                'batch' => 2
            ]);
            echo "Marked update_aib_team_stations_structure as run.\n";
        }
    } else {
        echo "Table aib_team_stations DOES NOT exist, migration should run.\n";
    }

} catch (\Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
