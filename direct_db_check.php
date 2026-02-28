<?php
$host = '127.0.0.1';
$db   = 'gestionale_cv';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$output = "";

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $output .= "CONNECTED\n";
     
     $stmt = $pdo->query("SELECT * FROM dashboard_sections");
     $rows = $stmt->fetchAll();
     
     $output .= "COUNT: " . count($rows) . "\n";
     foreach($rows as $row) {
         $output .= "ID: {$row['id']} | TITLE: {$row['title']} | ACTIVE: {$row['is_active']} | ROUTE: {$row['route']}\n";
     }
} catch (\PDOException $e) {
     $output .= "DB ERROR: " . $e->getMessage() . "\n";
}

file_put_contents('direct_check_output.txt', $output);
echo "FINISHED";
