<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Check current enum values
$col = $DB->select("SHOW COLUMNS FROM users WHERE Field = 'status'");
echo "Attuale: {$col[0]->Type}\n";

// Add 'rejected' to the enum
$DB->statement("ALTER TABLE users MODIFY COLUMN `status` ENUM('pending','active','rejected','suspended') DEFAULT 'pending'");

$col = $DB->select("SHOW COLUMNS FROM users WHERE Field = 'status'");
echo "Aggiornato: {$col[0]->Type}\n";
echo "Fatto!";
