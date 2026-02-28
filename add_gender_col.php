<?php
// Add gender column to users table
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain');

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schema = \Illuminate\Support\Facades\Schema::getFacadeRoot();

if (!$schema->hasColumn('users', 'gender')) {
    $schema->table('users', function ($table) {
        $table->enum('gender', ['male', 'female'])->nullable()->after('surname');
    });
    echo "OK: gender column added to users\n";
} else {
    echo "SKIP: gender column already exists\n";
}
