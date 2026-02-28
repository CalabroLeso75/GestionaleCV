<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DashboardSection;

$log = "FINAL VERIFICATION: " . date('Y-m-d H:i:s') . "\n";

try {
    $sections = DB::table('dashboard_sections')->get();
    $log .= "Total sections: " . $sections->count() . "\n";
    foreach($sections as $s) {
        $log .= "ID: {$s->id} | Title: {$s->title} | Active: {$s->is_active} | Role: {$s->required_role} | Area: " . ($s->required_area ?? 'NULL') . "\n";
    }
    
    $user = User::where('email', 'raffaele.cusano@calabriaverde.eu')->first();
    if($user) {
        $log .= "User: {$user->email}\n";
        $log .= "Super-Admin: " . ($user->hasRole('super-admin') ? 'YES' : 'NO') . "\n";
        
        $visible = DashboardSection::visibleTo($user);
        $log .= "Visible count: " . $visible->count() . "\n";
        foreach($visible as $v) {
            $log .= "  - {$v->title}\n";
        }
    }
} catch (\Exception $e) {
    $log .= "ERROR: " . $e->getMessage() . "\n";
}

file_put_contents('final_checker_output.txt', $log);
echo "DONE";
