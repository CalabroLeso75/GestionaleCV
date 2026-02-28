<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain; charset=utf-8');

// anag_persone columns
echo "=== COLONNE anag_persone ===\n";
$cols = Schema::getColumnListing('anag_persone');
foreach ($cols as $c) { echo "  - $c\n"; }

// First 3 rows
echo "\n=== PRIME 3 RIGHE ===\n";
$rows = DB::table('anag_persone')->limit(3)->get();
foreach ($rows as $r) {
    print_r((array)$r);
    echo "\n";
}

// Check if internal_employees exists
echo "\n=== TABELLA internal_employees ===\n";
if (Schema::hasTable('internal_employees')) {
    echo "ESISTE\n";
    $cols = Schema::getColumnListing('internal_employees');
    foreach ($cols as $c) { echo "  - $c\n"; }
    echo "Righe: " . DB::table('internal_employees')->count() . "\n";
} else {
    echo "NON ESISTE\n";
}

// Check all tables
echo "\n=== TUTTE LE TABELLE ===\n";
$tables = DB::select('SHOW TABLES');
$dbName = env('DB_DATABASE');
$key = "Tables_in_{$dbName}";
foreach ($tables as $t) {
    $tn = $t->$key;
    $count = DB::table($tn)->count();
    echo "  {$tn}: {$count} righe\n";
}

// Users
echo "\n=== USERS ===\n";
$users = DB::table('users')->get();
foreach ($users as $u) {
    echo "  ID:{$u->id} | {$u->name} {$u->surname} | {$u->email} | status:{$u->status} | type:{$u->type}\n";
}

// User columns
echo "\n=== COLONNE users ===\n";
$cols = Schema::getColumnListing('users');
foreach ($cols as $c) { echo "  - $c\n"; }
