<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Stations: " . \App\Models\AibStation::count() . " Active: " . \App\Models\AibStation::where('stato', 'Attivo')->count() . "\n";
echo "Vehicles: " . \App\Models\Vehicle::count() . " Available: " . \App\Models\Vehicle::where('stato', 'disponibile')->count() . "\n";
echo "Employees: " . \App\Models\InternalEmployee::count() . " AIB: " . \App\Models\InternalEmployee::where('is_aib_qualified', 1)->count() . "\n";
echo "Phones: " . \App\Models\CompanyPhone::count() . " Active: " . \App\Models\CompanyPhone::where('stato', 'Attivo')->count() . "\n";
