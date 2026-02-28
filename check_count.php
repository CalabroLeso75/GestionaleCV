<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Incident;

$count = Incident::count();
file_put_contents('test_count.txt', $count);
echo "Count: $count\n";
