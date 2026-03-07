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

echo "Logging in...\n";
$html = request("$baseUrl/login");
preg_match('/<input type="hidden" name="_token" value="([^"]+)"/', $html, $matches);
$csrf = $matches[1] ?? '';
request("$baseUrl/login", true, ['_token' => $csrf, 'email' => $email, 'password' => $password]);

$html = request("$baseUrl/admin/filemanager");
preg_match('/<meta name="csrf-token" content="([^"]+)">/', $html, $matches);
$csrf = $matches[1] ?? '';

$headers = ['X-CSRF-TOKEN: ' . $csrf, 'Content-Type: application/json', 'Accept: application/json'];

function api($endpoint, $payload) {
    global $baseUrl, $headers;
    return request("$baseUrl$endpoint", true, json_encode($payload), $headers);
}

// Write a test script to query the database
$phpScript = <<<'EOD'
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
try {
    $count = DB::table('localizz_comune')->whereNotNull('polygon')->count();
    echo "Cities with polygons: $count\n";
    if ($count == 0) {
        echo "ERROR: The localizz_comune table on the production server has NO polygons.\n";
    } else {
        // Test spatial query
        $point = "POINT(16.25 39.30)";
        $city = DB::select("SELECT id, name FROM localizz_comune WHERE polygon IS NOT NULL AND ST_Contains(polygon, ST_GeomFromText(?, 4326)) LIMIT 1", [$point]);
        if ($city) {
            echo "Found city (with 4326): " . $city[0]->name . "\n";
        } else {
            echo "No city found (with 4326).\n";
        }

        // Test spatial query without ST_GeomFromText SRID 4326
        $city2 = DB::select("SELECT id, name FROM localizz_comune WHERE polygon IS NOT NULL AND ST_Contains(polygon, ST_GeomFromText(?)) LIMIT 1", [$point]);
        if ($city2) {
            echo "Found city (without 4326): " . $city2[0]->name . "\n";
        } else {
            echo "No city found (without 4326).\n";
        }
    }
} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
EOD;

api('/admin/filemanager/write', ['path' => 'public/test_spatial.php', 'content' => $phpScript]);

echo "Running test query...\n";
echo request("$baseUrl/test_spatial.php") . "\n";

api('/admin/filemanager/delete', ['path' => 'public/test_spatial.php']);

unlink($cookieJar);
echo "DONE.\n";
