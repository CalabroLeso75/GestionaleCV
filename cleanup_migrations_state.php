<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Insert back into migrations
$exists1 = \Illuminate\Support\Facades\DB::table('migrations')->where('migration', '2026_02_27_122401_create_company_phones_table')->exists();
if (!$exists1) {
    \Illuminate\Support\Facades\DB::table('migrations')->insert([
        'migration' => '2026_02_27_122401_create_company_phones_table',
        'batch' => 10
    ]);
}

$exists2 = \Illuminate\Support\Facades\DB::table('migrations')->where('migration', '2026_02_27_122401_update_aib_team_advanced_structures')->exists();
if (!$exists2) {
    \Illuminate\Support\Facades\DB::table('migrations')->insert([
        'migration' => '2026_02_27_122401_update_aib_team_advanced_structures',
        'batch' => 10
    ]);
}

echo "Cleaned up migrations table.";
