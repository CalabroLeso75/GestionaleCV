<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = Schema::getColumnListing('vehicles');
file_put_contents('final_schema_check.txt', implode("\n", $cols));
echo "Done\n";
