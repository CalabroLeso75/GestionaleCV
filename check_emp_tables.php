<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "=== INTERNAL_EMPLOYEES ===\n";
$cols = $DB->select("SHOW COLUMNS FROM internal_employees");
foreach ($cols as $c) echo "  {$c->Field}: {$c->Type}\n";

echo "\n=== EXTERNAL_EMPLOYEES ===\n";
$cols2 = $DB->select("SHOW COLUMNS FROM external_employees");
foreach ($cols2 as $c) echo "  {$c->Field}: {$c->Type}\n";

echo "\n=== Users with employee links ===\n";
$users = $DB->select("SELECT id, name, surname, type, internal_employee_id FROM users");
foreach ($users as $u) {
    echo "  ID:{$u->id} | {$u->name} {$u->surname} | type:{$u->type} | emp_id:{$u->internal_employee_id}\n";
}
