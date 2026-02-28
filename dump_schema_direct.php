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

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$tables = ['users', 'internal_employees', 'external_employees', 'activity_logs', 'organizations', 'area_roles', 'system_areas'];
$schema = [];

foreach ($tables as $table) {
    try {
        $columns = $pdo->query("DESCRIBE `$table`")->fetchAll();
        $schema[$table] = $columns;
    } catch (\Exception $e) {
        // Table might not exist yet
    }
}

echo json_encode($schema, JSON_PRETTY_PRINT);
