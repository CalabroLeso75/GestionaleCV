<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

$log = "CHECK STARTED: " . date('Y-m-d H:i:s') . "\n";

try {
    $count = DB::table('dashboard_sections')->count();
    $log .= "Total sections: $count\n";
    
    $sections = DB::table('dashboard_sections')->get();
    foreach($sections as $s) {
        $log .= "ID: {$s->id} | Title: {$s->title} | Active: {$s->is_active} | Order: {$s->sort_order}\n";
    }
    
    $user = User::where('email', 'raffaele.cusano@calabriaverde.eu')->first();
    if($user) {
        $log .= "User found: yes\n";
        $log .= "User Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n";
    } else {
        $log .= "User not found\n";
    }
    
} catch (\Exception $e) {
    $log .= "ERROR: " . $e->getMessage() . "\n";
}

file_put_contents('checker_output.txt', $log);
echo "SUCCESS";
