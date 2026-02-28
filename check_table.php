<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$hasTable = \Illuminate\Support\Facades\Schema::hasTable('company_phones');
echo "Has table company_phones: " . ($hasTable ? 'YES' : 'NO') . "\n";

$migrations = \Illuminate\Support\Facades\DB::table('migrations')->orderBy('id', 'desc')->limit(5)->get();
foreach ($migrations as $m) {
    echo "Migration: {$m->migration}\n";
}
file_put_contents('db_dump.txt', "Has table: " . ($hasTable ? 'YES' : 'NO') . "\n" . json_encode($migrations));
