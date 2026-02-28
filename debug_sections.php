<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\DashboardSection;

$sections = DashboardSection::all();
$output = "Count: " . $sections->count() . "\n";
foreach ($sections as $s) {
    $output .= "ID: {$s->id} | Title: {$s->title} | Active: " . ($s->is_active ? 'YES' : 'NO') . " | Route: {$s->route}\n";
}
file_put_contents('debug_sections.txt', $output);
echo "Written to debug_sections.txt\n";
