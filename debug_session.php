<?php

$storagePath = __DIR__ . '/storage/framework/sessions';
$writable = is_writable($storagePath);
$files = glob($storagePath . '/*');
$count = count($files);

echo "<h1>Session Debug</h1>";
echo "<p>Storage Path: $storagePath</p>";
echo "<p>Writable: " . ($writable ? 'YES' : 'NO') . "</p>";
echo "<p>Session Files: $count</p>";

// Test writing
if ($writable) {
    file_put_contents($storagePath . '/test_session', 'test');
    echo "<p>Write Test: OK</p>";
} else {
    echo "<p>Write Test: FAILED</p>";
}

echo "<h2>Cookie Info</h2>";
print_r($_COOKIE);
