<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Fix route to use full URL
$DB->table('dashboard_sections')
    ->where('title', 'Risorse Umane')
    ->update(['route' => url('/hr')]);

echo "Route updated to: " . url('/hr') . "\n";

// Test routes
echo "\nRoute list:\n";
echo "  hr.index => " . route('hr.index') . "\n";
echo "  hr.internal.index => " . route('hr.internal.index') . "\n";
echo "  hr.external.index => " . route('hr.external.index') . "\n";
