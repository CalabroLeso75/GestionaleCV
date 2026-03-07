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
    die("No CSRF token found on login page\n");
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
if (empty($matches[1])) {
    die("No CSRF token found on filemanager. Login might have failed.\n");
}
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

echo "Running commands on LIVE server...\n";

// 1. Clear caches
echo "Cache Clear: " . api('/admin/filemanager/exec', ['command' => 'php artisan cache:clear']) . "\n";
echo "Config Clear: " . api('/admin/filemanager/exec', ['command' => 'php artisan config:clear']) . "\n";
echo "View Clear: " . api('/admin/filemanager/exec', ['command' => 'php artisan view:clear']) . "\n";
echo "Route Clear: " . api('/admin/filemanager/exec', ['command' => 'php artisan route:clear']) . "\n";

// 2. Clear OPcache if needed using a small script upload/execute logic or artisan command if supported
// Here we just use the filemanager to create a helper and visit it
$opcacheHelper = '<?php clearstatcache(); if(function_exists("opcache_reset")) opcache_reset(); echo "OPCACHE CLEARED";';
api('/admin/filemanager/write', ['path' => 'public/clear_opc.php', 'content' => $opcacheHelper]);
echo "Opcache Clear: " . request("$baseUrl/clear_opc.php") . "\n";

// 3. Storage link
echo "Storage Link: " . api('/admin/filemanager/exec', ['command' => 'php artisan storage:link']) . "\n";

// Cleanup
api('/admin/filemanager/delete', ['path' => 'public/clear_opc.php']);

unlink($cookieJar);
echo "DONE.\n";
