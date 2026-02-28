<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Rimozione Colonne 'istat_code'</h1>";

$host = '127.0.0.1';
$db   = 'gestionale_cv';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "<p style='color:green'>✅ Connesso al Database.</p>";
} catch (\PDOException $e) {
    die("Errore Connessione: " . $e->getMessage());
}

$tables = [
    'localizz_regione', 
    'localizz_provincia', 
    'localizz_comune'
];

foreach ($tables as $table) {
    echo "<h3>Tabella: $table</h3>";
    try {
        // Check if column exists first? 
        // Or just DROP COLUMN IF EXISTS (MySQL 8.0 support IF EXISTS, older versions check info_schema)
        // Let's rely on try/catch
        
        $sql = "ALTER TABLE `$table` DROP COLUMN `istat_code`";
        $pdo->exec($sql);
        echo "<p style='color:green'>✅ Colonna 'istat_code' rimossa.</p>";
        
    } catch (\PDOException $e) {
        // Check error code, 42000/1091 = Can't DROP 'x'; check that column/key exists
        if (strpos($e->getMessage(), "check that column/key exists") !== false) {
             echo "<p style='color:orange'>⚠️ Colonna già assente.</p>";
        } else {
             echo "<p style='color:red'>❌ Errore: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h1>Operazione Completata!</h1>";
