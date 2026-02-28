<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();
$Hash = \Illuminate\Support\Facades\Hash::getFacadeRoot();

$newPassword = 'Emilia73*';
$hashed = $Hash->make($newPassword);

$DB->table('users')->where('id', 1)->update([
    'password' => $hashed,
]);

echo "<h2>✅ Password aggiornata!</h2>";
echo "<p>Email: <code>raffaele.cusano@calabriaverde.eu</code></p>";
echo "<p>Nuova password: <code>{$newPassword}</code></p>";
echo "<p>Hash generato: <code>{$hashed}</code></p>";

// Verify
$user = $DB->table('users')->where('id', 1)->first();
$check = $Hash->check($newPassword, $user->password);
echo "<p>Verifica hash: " . ($check ? "✅ CORRETTA" : "❌ ERRORE") . "</p>";

echo "<hr><p><a href='/GestionaleCV/login'>🔐 Vai al Login</a></p>";
