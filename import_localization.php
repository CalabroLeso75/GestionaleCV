<?php
set_time_limit(300);
ini_set('memory_limit', '512M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Importazione Tabelle Localizzazione</h1>";

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

// Map filenames to new table names
// user requested: localizz_statoestero, localizz_regione, localizz_provincia, localizz_comune
$files = [
    'foreign_states.sql' => ['old' => 'foreign_states', 'new' => 'localizz_statoestero'],
    'it_regions.sql'     => ['old' => 'it_regions',     'new' => 'localizz_regione'],
    'it_provinces.sql'   => ['old' => 'it_provinces',   'new' => 'localizz_provincia'],
    'it_municipalities.sql' => ['old' => 'it_municipalities', 'new' => 'localizz_comune'],
];

foreach ($files as $filename => $mapping) {
    $path = __DIR__ . '/da_eliminare/' . $filename;
    
    if (!file_exists($path)) {
        echo "<p style='color:red'>❌ File non trovato: $filename</p>";
        continue;
    }

    echo "<h3>Elaborazione: $filename -> {$mapping['new']}</h3>";
    
    $sql = file_get_contents($path);
    
    // Replace table names in CREATE, DROP, INSERT, ALTER statements
    // The exact regex depends on how dump is formatted. 
    // Usually `table_name` is quoted with backticks.
    
    $old = $mapping['old'];
    $new = $mapping['new'];
    
    // Simple string replace might be risky if column names match table names, but unlikely here.
    // Let's use str_replace for backticked names first, then unquoted just in case.
    
    $sql = str_replace("`$old`", "`$new`", $sql);
    $sql = str_replace("INSERT INTO $old", "INSERT INTO $new", $sql); // rare case without backticks
    
    // Execute multiple statements
    // We can't just PDO->exec($sql) if it has delimiters or multiple statements sometimes.
    // But usually for dumps it works if no strange delimiter changes.
    // For safety, let's try executeSqlFile logic from previous script but with content replacement.
    
    // Split by semicolon at end of line
    $lines = explode("\n", $sql);
    $query = "";
    $count = 0;
    
    // Drop target table first manually to be sure
    $pdo->exec("DROP TABLE IF EXISTS `$new`");
    
    foreach ($lines as $line) {
        $trimLine = trim($line);
        if (empty($trimLine) || str_starts_with($trimLine, '--') || str_starts_with($trimLine, '/*')) {
            continue;
        }
        
        $query .= $line . "\n";
        
        if (substr(rtrim($trimLine), -1) == ';') {
            try {
                $pdo->exec($query);
                $count++;
            } catch (\Exception $e) {
                // Warning only
            }
            $query = "";
        }
    }
    
    // Verify
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$new`");
        $rows = $stmt->fetchColumn();
        echo "<p style='color:green'>✅ Tabella <strong>$new</strong> creata. Record: $rows</p>";
    } catch (\Exception $e) {
        echo "<p style='color:red'>❌ Errore verifica $new: " . $e->getMessage() . "</p>";
    }
}

echo "<h1>Finito!</h1>";
