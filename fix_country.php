<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "<h1>Fix paese nascita admin</h1>";

// Find USA using correct column name
$usa = $DB->table('localizz_statoestero')
    ->where('name_it', 'LIKE', '%Stati Uniti%')
    ->orWhere('name_it', 'LIKE', '%United States%')
    ->first();

if ($usa) {
    echo "<p>✅ Trovato: <strong>{$usa->name_it}</strong> (ID: {$usa->id})</p>";
    $DB->table('users')->where('id', 1)->update(['birth_country_id' => $usa->id]);
    echo "<p>✅ Utente admin aggiornato con birth_country_id = {$usa->id}</p>";
} else {
    echo "<p>⚠️ Non trovato. Cerco simili...</p>";
    $all = $DB->table('localizz_statoestero')
        ->where('name_it', 'LIKE', '%Americ%')
        ->orWhere('name_it', 'LIKE', '%Unit%')
        ->orWhere('name_it', 'LIKE', '%Stat%')
        ->get();
    echo "<table border='1'><tr><th>ID</th><th>name_it</th></tr>";
    foreach ($all as $c) {
        echo "<tr><td>{$c->id}</td><td>{$c->name_it}</td></tr>";
    }
    echo "</table>";
}
