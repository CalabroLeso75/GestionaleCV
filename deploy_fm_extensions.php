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

$files = [
    'app/Http/Controllers/Admin/FileManagerController.php',
    'resources/views/admin/filemanager/index.blade.php',
    'routes/web.php'
];

foreach ($files as $file) {
    echo "Uploading updated file $file...\n";
    $content = file_get_contents(__DIR__ . '/' . $file);
    $res = api('/admin/filemanager/write', ['path' => $file, 'content' => $content]);
    echo $res . "\n";
}

echo "DONE.\n";
unlink($cookieJar);
