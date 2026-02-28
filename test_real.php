<?php
// Physical file verification & auto-fix
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Diagnostica e Riparazione Database</h1>";

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
    echo "<p style='color:green'>✅ Connessione al Database: SUCCESSO</p>";
    
    // 1. Check if anag_persone exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'anag_persone'");
    $anagExists = $stmt->fetch();
    
    if (!$anagExists) {
        echo "<h2 style='color:red'>❌ Tabella 'anag_persone' NON trovata.</h2>";
        echo "<p>Devi importare il file <code>da_eliminare/anag_persone.sql</code> usando phpMyAdmin.</p>";
        exit; // Cannot proceed without source data
    }
    
    echo "<p style='color:green'>✅ Tabella 'anag_persone' trovata. Procedo con la migrazione...</p>";
    
    // 2. Check if employees exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'employees'");
    $empExists = $stmt->fetch();
    
    if (!$empExists) {
        echo "<p>Creazione tabella 'employees' in corso...</p>";
        
        $sqlPath = __DIR__ . '/laravel/migration_script.sql';
        if (!file_exists($sqlPath)) {
             echo "<p style='color:red'>❌ File script SQL non trovato in: $sqlPath</p>";
             exit;
        }
        
        $sql = file_get_contents($sqlPath);
        
        // Split SQL by semicolon to execute statements individually if needed, 
        // but PDO might execute multiple queries if supported by driver/config. 
        // Let's try executing the whole block first (CREATE + INSERT).
        // Since migration_script.sql has multiple statements, we process them.
        
        $pdo->exec($sql);
        echo "<p style='color:green'>✅ Tabella 'employees' creata e popolata!</p>";
        
    } else {
        echo "<p>Tabella 'employees' già esistente.</p>";
    }

    // 3. Final Verification
    $stmt = $pdo->query("SELECT COUNT(*) FROM employees");
    $count = $stmt->fetchColumn();
    echo "<h1>Totale Dipendenti: $count</h1>";
    
    if ($count > 4000) {
        echo "<h2 style='color:green'>TUTTO OK! Puoi tornare a Laravel.</h2>";
    } else {
        echo "<h2 style='color:orange'>Attenzione: numero dipendenti basso ($count). Verificare l'importazione.</h2>";
    }

} catch (\PDOException $e) {
    echo "<p style='color:red'>❌ Errore: " . $e->getMessage() . "</p>";
}
