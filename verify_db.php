<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

$section = $DB->table('dashboard_sections')->where('title', 'LIKE', '%Protezione%')->first();
echo "Section: " . ($section->title ?? 'NOT FOUND') . "\n";

$count = $DB->table('system_areas')->where('sort_order', '>=', 100)->count();
echo "Sub-areas Count: " . $count . "\n";

$areas = $DB->table('system_areas')->where('sort_order', '>=', 100)->pluck('name');
foreach ($areas as $name) {
    echo "  - " . $name . "\n";
}
