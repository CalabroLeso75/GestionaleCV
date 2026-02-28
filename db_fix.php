<?php
header('Content-Type: text/plain; charset=utf-8');
$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Checking external_employees ===\n";
$cols = $pdo->query("SHOW COLUMNS FROM external_employees LIKE 'is_aib'")->fetch();
if ($cols) {
    echo "  Column is_aib exists in external_employees\n";
} else {
    echo "  Column IS MISSING in external_employees. Adding now...\n";
    $pdo->exec("ALTER TABLE external_employees ADD COLUMN is_aib BOOLEAN DEFAULT FALSE AFTER job_title");
    echo "  Column added to external_employees\n";
}

echo "\n=== Checking organizations ===\n";
$cols = $pdo->query("SHOW COLUMNS FROM organizations LIKE 'is_aib'")->fetch();
if ($cols) {
    echo "  Column is_aib exists in organizations\n";
} else {
    echo "  Column IS MISSING in organizations. Adding now...\n";
    $pdo->exec("ALTER TABLE organizations ADD COLUMN is_aib BOOLEAN DEFAULT FALSE AFTER vat_number");
    echo "  Column added to organizations\n";
}

echo "\nDone\n";
