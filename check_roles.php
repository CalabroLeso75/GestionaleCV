<?php
header('Content-Type: text/plain; charset=utf-8');
$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Roles ===\n";
try {
    $rows = $pdo->query("SELECT name FROM roles")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($rows as $r) {
        echo "  $r\n";
    }
} catch (Exception $e) { echo $e->getMessage(); }
echo "Done\n";
