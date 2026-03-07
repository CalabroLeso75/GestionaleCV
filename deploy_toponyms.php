<?php
$baseUrl = 'https://smart-cv.it';
$email = 'raffaele.cusano@calabriaverde.eu';
$password = 'password';

$cookieJar = tempnam(sys_get_temp_dir(), 'cookie');

function request($url, $post = false, $data = [], $headers = []) {
    global $cookieJar;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        if (is_array($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

echo "Fetching login page...\n";
$html = request("$baseUrl/login");
preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $html, $matches);
if (empty($matches[1])) {
    die("No CSRF token found\n");
}
$csrf = $matches[1];

echo "Logging in...\n";
$loginRes = request("$baseUrl/login", true, [
    '_token' => $csrf,
    'email' => $email,
    'password' => $password
]);

echo "Going to filemanager...\n";
$html = request("$baseUrl/admin/filemanager");
preg_match('/<meta name="csrf-token" content="([^"]+)">/', $html, $matches);
if (empty($matches[1])) die("No CSRF token found on filemanager\n");
$csrf = $matches[1];

$headers = [
    'X-CSRF-TOKEN: ' . $csrf,
    'Content-Type: application/json',
    'Accept: application/json'
];

function api($endpoint, $payload) {
    global $baseUrl, $headers;
    return request("$baseUrl$endpoint", true, json_encode($payload), $headers);
}

// Ensure folders
api('/admin/filemanager/mkdir', ['path' => 'app/Models', 'name' => 'Location']);
api('/admin/filemanager/mkdir', ['path' => 'app/Console', 'name' => 'Commands']);

$files = [
    'app/Models/Toponym.php',
    'app/Console/Commands/ImportToponyms.php',
    'app/Console/Commands/ParseKmlPolygons.php',
    'app/Http/Controllers/Api/LocationController.php',
    'app/Http/Controllers/DosController.php',
    'routes/web.php',
    'resources/views/dos/fire_management.blade.php',
    'resources/views/emails/dos_report.blade.php',
    'database/migrations/2026_03_06_143200_create_toponyms_table.php',
    'database/migrations/2026_03_06_145238_add_polygon_to_localizz_comune_table.php'
];

foreach ($files as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        echo "Missing file locally: $file\n";
        continue;
    }
    echo "Uploading updated file $file...\n";
    $content = file_get_contents(__DIR__ . '/' . $file);
    $res = api('/admin/filemanager/write', ['path' => $file, 'content' => $content]);
    echo substr($res, 0, 100) . "\n";
}

echo "Uploading SQL Data Dump...\n";
$sqlContent = file_get_contents(__DIR__ . '/deploy_geo_data.sql');
$res = api('/admin/filemanager/write', ['path' => 'storage/app/deploy_geo_data.sql', 'content' => $sqlContent]);
echo substr($res, 0, 100) . "\n";

echo "Running migrations and DB sync...\n";
$runnerCode = "<?php
require __DIR__.'/../vendor/autoload.php';
\$app = require_once __DIR__.'/../bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_03_06_143200_create_toponyms_table.php',
        '--force' => true
    ]);
    
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_03_06_145238_add_polygon_to_localizz_comune_table.php',
        '--force' => true
    ]);
    
    \$sqlPath = storage_path('app/deploy_geo_data.sql');
    if (file_exists(\$sqlPath)) {
        \Illuminate\Support\Facades\DB::unprepared(file_get_contents(\$sqlPath));
        echo 'SQL IMPORT COMPLETED OK. ';
        unlink(\$sqlPath);
    }
    
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    
    echo 'MIGRATION AND CACHE CLEARED SECURELY OK';
} catch (\Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage();
}
";

api('/admin/filemanager/write', ['path' => 'public/_deploy_runner.php', 'content' => $runnerCode]);

$runnerResult = request("$baseUrl/_deploy_runner.php");
echo "Runner Result: " . $runnerResult . "\n";

api('/admin/filemanager/delete', ['paths' => ['public/_deploy_runner.php']]);

echo "DONE.\n";
unlink($cookieJar);
