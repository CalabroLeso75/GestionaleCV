<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: application/json');
echo json_encode([
    'locations' => \Illuminate\Support\Facades\Schema::getColumnListing('locations'),
    'aib_stations' => \Illuminate\Support\Facades\Schema::getColumnListing('aib_stations')
]);
