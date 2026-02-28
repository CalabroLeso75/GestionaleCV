<?php
header('Content-Type: text/plain; charset=utf-8');
$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check for organizations-related tables
echo "=== Tables matching 'organ' ===\n";
$tables = $pdo->query("SHOW TABLES LIKE '%organ%'")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    echo "  $t\n";
}
if (empty($tables)) echo "  (none found)\n";

// Check if organizations table exists and its structure
if (in_array('organizations', $tables)) {
    echo "\n=== organizations table structure ===\n";
    $cols = $pdo->query("SHOW COLUMNS FROM organizations");
    foreach ($cols as $c) {
        echo "  {$c['Field']} ({$c['Type']})\n";
    }
    
    echo "\n=== organizations sample records ===\n";
    $rows = $pdo->query("SELECT * FROM organizations LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "  " . json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
    echo "  Total in sample: " . count($rows) . "\n";
} else {
    echo "\n=== organizations table NOT found ===\n";
}

echo "\nDone\n";
