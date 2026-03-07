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

\Illuminate\Support\Facades\Auth::login($user);

// Renderizza la view del dashboard
$view = view('dashboard')->render();

// Estrai i titoli delle card
preg_match_all('/<h5 class="card-title"[^>]*>(.*?)<\/h5>/', $view, $matches);
echo "Tessere Trovate nel HTML (per Raffaele Cusano):\n";
foreach ($matches[1] as $title) {
    echo "- " . trim(strip_tags($title)) . "\n";
}
