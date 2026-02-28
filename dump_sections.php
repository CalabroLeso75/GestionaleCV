<?php
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sections = DB::table('dashboard_sections')->get();
$fp = fopen('debug_sections.txt', 'w');
fwrite($fp, "Total sections: " . count($sections) . PHP_EOL);
foreach ($sections as $s) {
    fwrite($fp, $s->id . ' | ' . $s->title . ' | ' . $s->is_active . ' | ' . $s->required_role . PHP_EOL);
}
fclose($fp);
echo "DONE\n";
