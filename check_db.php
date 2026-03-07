<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'raffaele.cusano@calabriaverde.eu')->first();
if (!$user) {
    echo "User not found\n";
    exit;
}
echo "User roles: " . $user->roles->pluck('name')->implode(', ') . "\n";
$sections = \App\Models\DashboardSection::visibleTo($user);
echo "Visible sections:\n";
foreach($sections as $s) {
    echo "- " . $s->title . " (" . $s->route . ") [Active: " . $s->is_active . "]\n";
}

$all = \App\Models\DashboardSection::all();
echo "\nAll sections:\n";
foreach($all as $s) {
    echo "- " . $s->title . " (" . $s->route . ") [Active: " . $s->is_active . "]\n";
}
