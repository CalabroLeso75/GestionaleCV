<?php

use App\Http\Controllers\AibStationController;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$controller = new AibStationController();

// Use Reflection to test private methods if needed, or just test store logic
$reflection = new ReflectionClass($controller);

$dmsToDecimal = $reflection->getMethod('dmsToDecimal');
$dmsToDecimal->setAccessible(true);

$decimalToDms = $reflection->getMethod('decimalToDms');
$decimalToDms->setAccessible(true);

$testData = [
    ['dms' => '39° 12\' 34.5"', 'expected' => 39.20958333],
    ['dms' => 'N 39° 0\' 0"', 'expected' => 39.0],
    ['dms' => 'S 39° 0\' 0"', 'expected' => -39.0],
];

echo "Testing Coordinate Conversion:\n";
foreach ($testData as $test) {
    $result = $dmsToDecimal->invoke($controller, $test['dms']);
    $status = abs($result - $test['expected']) < 0.0001 ? "OK" : "FAIL";
    echo "DMS: {$test['dms']} -> Result: $result (Expected: {$test['expected']}) -> $status\n";
}

$decimalTest = 39.20958333;
$resultDms = $decimalToDms->invoke($controller, $decimalTest, 'lat');
echo "Decimal: $decimalTest -> DMS: $resultDms\n";
