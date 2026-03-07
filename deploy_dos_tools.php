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
    echo $html;
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

// 1. Create Directories if not exist
echo "Ensuring remote directories exist...\n";
api('/admin/filemanager/mkdir', ['path' => 'resources/views', 'name' => 'dos']);
api('/admin/filemanager/mkdir', ['path' => 'resources/views', 'name' => 'emails']);
api('/admin/filemanager/mkdir', ['path' => 'resources/views/admin', 'name' => 'email_recipients']);
api('/admin/filemanager/mkdir', ['path' => 'resources/views/admin', 'name' => 'dbmanager']);

$files = [
    'bootstrap/app.php',
    'app/Http/Controllers/DosController.php',
    'app/Http/Controllers/LocationController.php',
    'app/Http/Controllers/Admin/FileManagerController.php',
    'app/Http/Controllers/Admin/EmailRecipientController.php',
    'app/Http/Controllers/Admin/DbManagerController.php',
    'app/Models/EmailRecipient.php',
    'database/migrations/2026_03_06_100000_create_email_recipients_table.php',
    'resources/views/dos/index.blade.php',
    'resources/views/dos/fire_management.blade.php',
    'resources/views/emails/dos_report.blade.php',
    'resources/views/admin/index.blade.php',
    'resources/views/admin/email_recipients/index.blade.php',
    'resources/views/admin/dbmanager/index.blade.php',
    'routes/web.php'
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

// 2. Add Dashboard Section via Tinker and Run Migrations securely using a temp script
echo "Running migrations and clearing remote cache...\n";
$runnerCode = "<?php
require __DIR__.'/../vendor/autoload.php';
\$app = require_once __DIR__.'/../bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--path' => 'database/migrations/2026_03_06_100000_create_email_recipients_table.php',
        '--force' => true
    ]);
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    
    // Fix Dashboard icon (max 20 chars in db)
    \App\Models\DashboardSection::where('title', 'Strumenti DOS')->update(['icon' => '🔥']);
    
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
