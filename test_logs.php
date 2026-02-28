<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ActivityLogger;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Simulate an admin user if not logged in (for CLI/Direct script access)
if (!Auth::check()) {
    $admin = User::where('email', 'raffaele.cusano@calabriaverde.eu')->first();
    if ($admin) Auth::login($admin);
}

echo "Testing Activity Logging...\n";

// Trigger a test update log
ActivityLogger::log('update', 'InternalEmployee', 1, "TEST LOG: Modifica mansione e note effettuata correttamente.");

// Check last 5 logs
echo "\nLast 5 logs:\n";
$logs = ActivityLog::orderByDesc('created_at')->limit(5)->get();
foreach ($logs as $log) {
    echo "[{$log->created_at}] Action: {$log->action} | Detail: {$log->details} | IP: {$log->ip_address}\n";
}

echo "\nVerification complete.\n";
