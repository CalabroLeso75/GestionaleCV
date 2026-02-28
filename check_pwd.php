<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "=== password_reset_tokens ===\n";
$cols = $DB->select("SHOW COLUMNS FROM password_reset_tokens");
foreach ($cols as $c) echo "  {$c->Field}: {$c->Type}\n";

echo "\n=== Records ===\n";
$tokens = $DB->table('password_reset_tokens')->get();
foreach ($tokens as $t) {
    echo "  email: {$t->email}\n";
    echo "  token (first 20): " . substr($t->token, 0, 20) . "...\n";
    echo "  created_at: {$t->created_at}\n";
    echo "---\n";
}
if ($tokens->isEmpty()) echo "  (nessun record)\n";

echo "\n=== APP_URL ===\n";
echo "  " . config('app.url') . "\n";

echo "\n=== Test route password.reset ===\n";
echo "  " . route('password.reset', ['token' => 'TEST_TOKEN', 'email' => 'test@test.com']) . "\n";

echo "\n=== Auth passwords config ===\n";
print_r(config('auth.passwords'));
