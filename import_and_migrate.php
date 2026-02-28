<?php
set_time_limit(300); // 5 minutes execution time
ini_set('memory_limit', '256M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Importazione Database via Web (Chunked)</h1>";

// 1. Configuration
$host = '127.0.0.1';
$db   = 'gestionale_cv';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$anagPath = __DIR__ . '/da_eliminare/anag_persone.sql';
$migrationPath = __DIR__ . '/laravel/migration_script.sql';

// 2. Connect
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_LOCAL_INFILE => true
    ]);
    echo "<p style='color:green'>✅ Connesso al Database.</p>";

    // Try to increase payload size if allowed (might fail if no permissions, but root usually ok)
    try {
        $pdo->exec("SET GLOBAL max_allowed_packet=16777216;"); // 16MB
    } catch (\Exception $e) {
        // Ignore if fails, proceed with chunking as backup
    }

} catch (\PDOException $e) {
    echo "<h2 style='color:red'>Errore Connessione: " . $e->getMessage() . "</h2>";
    echo "<p>Provo con localhost...</p>";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=$db;charset=$charset", $user, $pass);
        echo "<p style='color:green'>✅ Connesso (localhost).</p>";
    } catch (\PDOException $ex) {
        die("<h2 style='color:red'>Errore Fatale: " . $ex->getMessage() . "</h2>");
    }
}

/**
 * Helper to execute large SQL file line by line
 */
function executeSqlFile($pdo, $filePath, $tableName) {
    if (!file_exists($filePath)) {
        echo "<h2 style='color:red'>File non trovato: $filePath</h2>";
        return false;
    }

    echo "<h3>Importazione '$tableName' da: " . basename($filePath) . "...</h3>";
    
    $handle = fopen($filePath, "r");
    if (!$handle) {
        echo "<h2 style='color:red'>Impossibile aprire il file.</h2>";
        return false;
    }

    $query = "";
    $count = 0;
    
    // First, drop table if exists to ensure clean slate
    try {
        $pdo->exec("DROP TABLE IF EXISTS `$tableName`");
    } catch (\Exception $e) {
        // Ignore
    }

    while (($line = fgets($handle)) !== false) {
        $trimLine = trim($line);
        
        // Skip comments and empty lines
        if (empty($trimLine) || str_starts_with($trimLine, '--') || str_starts_with($trimLine, '/*')) {
            continue;
        }

        $query .= $line;

        // If line ends with semicolon, execute query
        if (substr(rtrim($trimLine), -1) == ';') {
            try {
                $pdo->exec($query);
                $count++;
            } catch (\PDOException $e) {
                echo "<p style='color:orange'>Warning allo statement $count: " . $e->getMessage() . "</p>";
                // Continue despite errors? Usually better to stop if CREATE fails, but INSERT might fail partially.
            }
            $query = "";
        }
    }

    fclose($handle);
    
    // Verify count
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM `$tableName`");
        $rowCount = $stmt->fetchColumn();
        echo "<p style='color:green'>✅ Importazione completata. Record in $tableName: <strong>$rowCount</strong></p>";
        return $rowCount;
    } catch (\Exception $e) {
        echo "<p style='color:red'>Errore verifica conteggio: " . $e->getMessage() . "</p>";
        return 0;
    }
}

// 3. Execute Step 1: anag_persone
$anagCount = executeSqlFile($pdo, $anagPath, 'anag_persone');

if ($anagCount === false) {
    die("Stop.");
}

// 4. Execute Step 2: employees migration logic
// Since migration_script.sql contains multiple statements (CREATE + INSERT SELECT + ...),
// we use the same function but tell it to ignore table name check or just pass dummy.
// Wait, the function drops table by name. 
// migration_script.sql creates 'employees' and inserts into it.
// We can use the function if we modify it or just copy logic.
// Let's use the function for employees table too.

echo "<hr>";
$empCount = executeSqlFile($pdo, $migrationPath, 'employees');

if ($empCount > 0) {
    echo "<h1>🎉 TUTTO FATTO!</h1>";
    echo "<p>Ora puoi tornare a usare Laravel.</p>";
} else {
    echo "<h2>Qualcosa è andato storto nella migrazione finale.</h2>";
}
