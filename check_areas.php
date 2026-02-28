<?php
header('Content-Type: text/plain; charset=utf-8');
$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check for organizations-related tables
echo "=== Tables matching 'org' ===\n";
$tables = $pdo->query("SHOW TABLES LIKE '%organ%'");
foreach ($tables as $t) { $v = array_values(get_object_vars($t)); echo "  " . $v[0] . "\n"; }

// Check FK constraints on external_employees
echo "\n=== FK constraints on external_employees ===\n";
$fks = $pdo->query("
    SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_NAME = 'external_employees' AND TABLE_SCHEMA = 'gestionale_cv' AND REFERENCED_TABLE_NAME IS NOT NULL
");
foreach ($fks as $f) {
    echo "  {$f['COLUMN_NAME']} -> {$f['REFERENCED_TABLE_NAME']}.{$f['REFERENCED_COLUMN_NAME']}\n";
}

// Check if organizations table exists and its structure
echo "\n=== organizations table ===\n";
try {
    $cols = $pdo->query("SHOW COLUMNS FROM organizations");
    foreach ($cols as $c) echo "  {$c['Field']} ({$c['Type']})\n";
    
    echo "\n=== organizations records ===\n";
    $rows = $pdo->query("SELECT * FROM organizations LIMIT 10");
    $count = 0;
    foreach ($rows as $r) { echo "  " . json_encode($r, JSON_UNESCAPED_UNICODE) . "\n"; $count++; }
    echo "  Count: $count\n";
} catch (Exception $e) {
    echo "  Not found: " . $e->getMessage() . "\n";
}

// Check HRController external methods
echo "\n=== routes for external ===\n";
echo "  (see web.php)\n";
