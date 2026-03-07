<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Force Magazzino to be completely visible and active
    DB::table('dashboard_sections')
        ->where('route', 'like', '%magazzino%')
        ->update([
            'is_active' => 1,
            'required_role' => null,
            'required_area' => null
        ]);
        
    // Validate if it exists
    $mag = DB::table('dashboard_sections')->where('route', 'like', '%magazzino%')->first();
    echo "Tile Magazzino: " . ($mag ? json_encode($mag) : "NOT FOUND") . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
