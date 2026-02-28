<?php
header('Content-Type: text/plain; charset=utf-8');
$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ensure test organizations exist
$pdo->exec("INSERT INTO organizations (name, type, tax_code, is_aib, created_at, updated_at) 
            VALUES ('Associazione Volontari AIB - Test', 'private', '99999999991', 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE is_aib = 1");

$pdo->exec("INSERT INTO organizations (name, type, tax_code, is_aib, created_at, updated_at) 
            VALUES ('Ditta Privata - Edilizia', 'private', '99999999992', 0, NOW(), NOW())
            ON DUPLICATE KEY UPDATE is_aib = 0");

$rows = $pdo->query("SELECT id, name, is_aib FROM organizations WHERE tax_code LIKE '9999%'")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "ID: {$r['id']}, Name: {$r['name']}, AIB: {$r['is_aib']}\n";
}
echo "Verification complete.\n";
