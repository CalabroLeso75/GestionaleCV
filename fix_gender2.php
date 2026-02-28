<?php
header('Content-Type: text/plain');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

$users = $DB->table('users')->get();
echo "ALL USERS:\n";
foreach ($users as $u) {
    echo "  ID:{$u->id} | {$u->name} {$u->surname} | gender:" . ($u->gender ?? 'NULL') . " | CF:{$u->fiscal_code}\n";
    
    if (empty($u->gender) && !empty($u->fiscal_code) && strlen($u->fiscal_code) >= 11) {
        $day = (int) substr(strtoupper($u->fiscal_code), 9, 2);
        $gender = $day > 40 ? 'female' : 'male';
        $DB->table('users')->where('id', $u->id)->update(['gender' => $gender]);
        echo "    --> FIXED to {$gender}\n";
    }
}
