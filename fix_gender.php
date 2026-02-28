<?php
header('Content-Type: text/plain');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

$u = $DB->table('users')->where('id', 1)->first();
echo "ID: {$u->id}\n";
echo "Name: {$u->name} {$u->surname}\n";
echo "Gender: " . ($u->gender ?? 'NULL') . "\n";
echo "CF: {$u->fiscal_code}\n";

// Fix if null
if (!$u->gender && $u->fiscal_code) {
    $day = (int) substr(strtoupper($u->fiscal_code), 9, 2);
    $gender = $day > 40 ? 'female' : 'male';
    $DB->table('users')->where('id', 1)->update(['gender' => $gender]);
    echo "\nFixed → {$gender} (giorno CF: {$day})\n";
} else {
    echo "\nGender already set.\n";
}
