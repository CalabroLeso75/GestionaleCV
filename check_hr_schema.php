<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "=== INTERNAL EMPLOYEES SCHEMA ===\n";
$cols = $DB->select("SHOW COLUMNS FROM internal_employees");
foreach ($cols as $c) echo "  {$c->Field}: {$c->Type} | Null:{$c->Null} | Key:{$c->Key}\n";
echo "\nTotal internal: " . $DB->table('internal_employees')->count() . "\n";

echo "\n=== EXTERNAL EMPLOYEES SCHEMA ===\n";
$cols2 = $DB->select("SHOW COLUMNS FROM external_employees");
foreach ($cols2 as $c) echo "  {$c->Field}: {$c->Type} | Null:{$c->Null} | Key:{$c->Key}\n";
echo "\nTotal external: " . $DB->table('external_employees')->count() . "\n";

echo "\n=== DASHBOARD_SECTIONS ===\n";
$secs = $DB->table('dashboard_sections')->get();
foreach ($secs as $s) echo "  ID:{$s->id} | {$s->title} | route:{$s->route} | icon:{$s->icon}\n";
