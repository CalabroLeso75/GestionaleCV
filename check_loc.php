<?php
header('Content-Type: text/plain');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "=== localizz_comune columns ===\n";
$cols = $DB->select("SHOW COLUMNS FROM localizz_comune");
foreach ($cols as $c) echo "  {$c->Field}: {$c->Type}\n";

echo "\n=== Prima riga ===\n";
$r = $DB->table('localizz_comune')->first();
print_r((array)$r);

echo "\n\n=== localizz_statoestero columns ===\n";
$cols2 = $DB->select("SHOW COLUMNS FROM localizz_statoestero");
foreach ($cols2 as $c) echo "  {$c->Field}: {$c->Type}\n";

echo "\n=== Prima riga ===\n";
$r2 = $DB->table('localizz_statoestero')->first();
print_r((array)$r2);
