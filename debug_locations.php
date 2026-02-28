<?php

use App\Models\Location\Province;
use App\Models\Location\Country;
use Illuminate\Support\Facades\Route;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h1>Location Data Debug</h1>";

// 1. Check Database Counts
try {
    $pCount = Province::count();
    $cCount = Country::count();
    echo "<p>Provinces in DB: <strong>$pCount</strong></p>";
    echo "<p>Countries in DB: <strong>$cCount</strong></p>";
    
    if ($pCount > 0) {
        echo "<p>First Province: " . Province::first()->name . "</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>DB Error: " . $e->getMessage() . "</p>";
}

// 2. Check Routes
echo "<h2>Registered Routes (API)</h2>";
$routes = Route::getRoutes();
foreach ($routes as $route) {
    if (strpos($route->uri(), 'api/') === 0) {
        echo $route->uri() . "<br>";
    }
}

echo "<h2>Test JSON Response</h2>";
// Simulate internal request? checking direct DB is enough usually.
// If DB has data, then likely JS issue or route issue.
