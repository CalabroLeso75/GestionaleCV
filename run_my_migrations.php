<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
$foundKeys = [];
foreach($tables as $t) {
    foreach($t as $key => $val) {
        $foundKeys[] = $val;
    }
}
echo "Has company_phones: " . (in_array('company_phones', $foundKeys) ? 'YES' : 'NO') . "\n";
echo "Has aib_team_phones: " . (in_array('aib_team_phones', $foundKeys) ? 'YES' : 'NO') . "\n";

echo "Attempting to delete from migrations and run migrate:\n";
\Illuminate\Support\Facades\DB::table('migrations')->whereIn('migration', [
    '2026_02_27_122401_create_company_phones_table',
    '2026_02_27_122401_update_aib_team_advanced_structures'
])->delete();

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
} catch (\Exception $e) {
    echo "ERROR:\n" . $e->getMessage() . "\n" . $e->getTraceAsString();
}
