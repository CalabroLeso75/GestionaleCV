<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$provCols = Illuminate\Support\Facades\Schema::getColumnListing('localizz_provincia');
$comuniCols = Illuminate\Support\Facades\Schema::getColumnListing('localizz_comune');

echo "Province columns: " . implode(', ', $provCols) . "\n";
echo "City columns: " . implode(', ', $comuniCols) . "\n";
