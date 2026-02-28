<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\DashboardSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

$output = "--- DEBUG DASHBOARD SECTIONS ---\n";

// 1. Current user check (simulation of the logged in user)
$email = 'raffaele.cusano@calabriaverde.eu'; // User mentioned it might be him
$user = User::where('email', $email)->first();

if (!$user) {
    $output .= "User not found: $email\n";
    // Get the first user just in case
    $user = User::first();
}

if ($user) {
    $output .= "User: {$user->name} {$user->surname} ({$user->email})\n";
    $output .= "Roles: " . json_encode($user->getRoleNames()) . "\n";
    $output .= "Has super-admin: " . ($user->hasRole('super-admin') ? 'YES' : 'NO') . "\n";
} else {
    $output .= "No users found in database.\n";
}

// 2. Sections check
$sections = DB::table('dashboard_sections')->get();
$output .= "\nTotal sections in DB: " . $sections->count() . "\n";
foreach ($sections as $s) {
    $output .= "ID: {$s->id} | Title: {$s->title} | Active: {$s->is_active} | Role: {$s->required_role} | Route: {$s->route}\n";
}

// 3. Visibility test
if ($user) {
    $visible = DashboardSection::visibleTo($user);
    $output .= "\nVisible to this user: " . $visible->count() . "\n";
    foreach ($visible as $v) {
        $output .= "  - {$v->title}\n";
    }
}

file_put_contents('final_debug_output.txt', $output);
echo "DONE\n";
