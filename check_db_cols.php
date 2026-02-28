<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "user_area_roles: " . implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('user_area_roles')) . "\n";
echo "system_areas: " . implode(', ', \Illuminate\Support\Facades\Schema::getColumnListing('system_areas')) . "\n";
