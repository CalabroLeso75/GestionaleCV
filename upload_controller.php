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

$file = 'app/Http/Controllers/Api/LocationController.php';
$content = file_get_contents(__DIR__ . '/' . $file);

echo "Uploading $file...\n";
$res = api('/admin/filemanager/write', ['path' => $file, 'content' => $content]);
echo "Write result: $res\n";

echo "Clearing opcache...\n";
$opcacheHelper = '<?php clearstatcache(); if(function_exists("opcache_reset")) opcache_reset(); echo "OPCACHE CLEARED";';
api('/admin/filemanager/write', ['path' => 'public/clear_opc.php', 'content' => $opcacheHelper]);
echo request("$baseUrl/clear_opc.php") . "\n";
api('/admin/filemanager/delete', ['path' => 'public/clear_opc.php']);

unlink($cookieJar);
echo "DONE.\n";
