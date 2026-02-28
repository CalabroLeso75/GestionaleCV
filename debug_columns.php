<?php

use App\Models\Location\Province;
use App\Models\Location\Country;
use App\Models\Location\City;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h1>DB Columns Debug</h1>";

echo "<h2>Province (First Record)</h2>";
$p = Province::first();
if ($p) {
    echo "<pre>" . print_r($p->toArray(), true) . "</pre>";
} else {
    echo "No provinces found.";
}

echo "<h2>Country (First Record)</h2>";
$c = Country::first();
if ($c) {
    echo "<pre>" . print_r($c->toArray(), true) . "</pre>";
} else {
    echo "No countries found.";
}

echo "<h2>City (First Record)</h2>";
$ci = City::first();
if ($ci) {
    echo "<pre>" . print_r($ci->toArray(), true) . "</pre>";
} else {
    echo "No cities found.";
}
