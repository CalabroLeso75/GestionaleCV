<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "App bootstrapped OK\n";
    
    $count = \Illuminate\Support\Facades\DB::table('users')->count();
    echo "Users count: {$count}\n";
    
    $cols = \Illuminate\Support\Facades\Schema::getColumnListing('users');
    echo "Users columns: " . implode(', ', $cols) . "\n";
    
    // Check if internal_employee_id exists
    $hasCol = \Illuminate\Support\Facades\Schema::hasColumn('users', 'internal_employee_id');
    echo "internal_employee_id: " . ($hasCol ? "EXISTS" : "MISSING") . "\n";
    
    if (!$hasCol) {
        \Illuminate\Support\Facades\Schema::table('users', function ($table) {
            $table->unsignedBigInteger('internal_employee_id')->nullable()->after('birth_country_id');
        });
        echo "internal_employee_id: ADDED!\n";
    }
    
    // Check localizz_statoestero for USA
    $countries = \Illuminate\Support\Facades\DB::table('localizz_statoestero')
        ->where('name', 'LIKE', '%Stat%')
        ->get(['id', 'name']);
    echo "\nCountries matching 'Stat':\n";
    foreach ($countries as $c) {
        echo "  ID:{$c->id} - {$c->name}\n";
    }
    
    echo "\n=== ALL CHECKS PASSED ===\n";
    
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
