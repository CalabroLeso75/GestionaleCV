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

$files = [
    'routes/web.php',
    'app/Http/Controllers/HRController.php',
    'app/Http/Controllers/Admin/UserManagementController.php',
    'resources/views/hr/internal/show.blade.php',
    'resources/views/admin/users/index.blade.php',
    'resources/views/admin/email_recipients/index.blade.php',
    'resources/views/dos/fire_management.blade.php',
    'resources/views/dos/history.blade.php',
    'resources/views/dashboard.blade.php',
    'resources/views/emails/dos_report.blade.php',
    'app/Http/Controllers/DosController.php',
    'app/Models/EmergencyReport.php',
    'database/migrations/2026_03_06_232503_create_emergency_reports_table.php',
    'public/sprites.php',
    '.htaccess'
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

echo "Running setup script and clearing cache...\n";
$runnerCode = "<?php
require __DIR__.'/../vendor/autoload.php';
\$app = require_once __DIR__.'/../bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \$roleDOS = \\Spatie\\Permission\\Models\\Role::findOrCreate('dos');
    echo 'Ruolo dos creato. ';

    \$pcSection = \\App\\Models\\DashboardSection::where('title', 'like', '%Protezione Civile%')->first();
    \$aibSection = \\App\\Models\\DashboardSection::where('title', 'like', '%A.I.B.%')->orWhere('route', 'like', '%pc/aib%')->first();
    \$dosSection = \\App\\Models\\DashboardSection::where('title', 'like', '%Strumenti D.O.S.%')->orWhere('route', 'like', '%dos%')->first();

    \$sectionIds = collect([\$pcSection, \$aibSection, \$dosSection])->filter()->pluck('id')->toArray();

    if (!empty(\$sectionIds)) {
        foreach (\$sectionIds as \$sid) {
            \\Illuminate\\Support\\Facades\\DB::table('role_sections')->updateOrInsert([
                'role_id' => \$roleDOS->id,
                'dashboard_section_id' => \$sid
            ]);
        }
        echo 'Sezioni assegnate. ';
    }

    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true,
        '--path' => 'database/migrations/2026_03_06_232503_create_emergency_reports_table.php'
    ]);
    echo 'DB Migrated DOS Table. ';

    \Illuminate\Support\Facades\Artisan::call('storage:link');
    echo 'Storage Linked. ';

    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');

    echo 'CACHE CLEARED SECURELY OK';
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
