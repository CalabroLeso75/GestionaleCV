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

request("$baseUrl/login");
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

// Write the test script
$phpScript = <<<'EOD'
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
try {
    $lat = 39.30;
    $lon = 16.25;

    // 1. Test LON LAT
    $point1 = "POINT($lon $lat)";
    $city1 = DB::select("SELECT id, name FROM localizz_comune WHERE ST_Contains(polygon, ST_GeomFromText(?, 4326)) LIMIT 1", [$point1]);
    echo "LON/LAT (XAMPP behavior): " . ($city1 ? $city1[0]->name : "Not found") . "\n";

    // 2. Test LAT LON (MySQL 8.0 default for 4326)
    $point2 = "POINT($lat $lon)";
    $city2 = DB::select("SELECT id, name FROM localizz_comune WHERE ST_Contains(polygon, ST_GeomFromText(?, 4326)) LIMIT 1", [$point2]);
    echo "LAT/LON (MySQL 8 default): " . ($city2 ? $city2[0]->name : "Not found") . "\n";

    // 3. Test Axis-order flag (MySQL 8)
    try {
        $city3 = DB::select("SELECT id, name FROM localizz_comune WHERE ST_Contains(polygon, ST_GeomFromText(?, 4326, 'axis-order=long-lat')) LIMIT 1", [$point1]);
        echo "LON/LAT with axis-order flag: " . ($city3 ? $city3[0]->name : "Not found") . "\n";
    } catch (\Exception $e) {
        echo "LON/LAT with axis-order flag: Error not supported\n";
    }

    // 4. Test swapping ST_Contains with ST_Within just in case
    $city4 = DB::select("SELECT id, name FROM localizz_comune WHERE ST_Within(ST_GeomFromText(?, 4326), polygon) LIMIT 1", [$point2]);
    echo "LAT/LON with ST_Within: " . ($city4 ? $city4[0]->name : "Not found") . "\n";

} catch (\Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
EOD;

api('/admin/filemanager/write', ['path' => 'public/test_spatial2.php', 'content' => $phpScript]);

echo request("$baseUrl/test_spatial2.php") . "\n";

api('/admin/filemanager/delete', ['path' => 'public/test_spatial2.php']);
unlink($cookieJar);
