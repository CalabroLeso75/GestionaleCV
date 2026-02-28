<?php
header('Content-Type: text/plain; charset=utf-8');
$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== activity_logs structure ===\n";
try {
    $cols = $pdo->query("SHOW COLUMNS FROM activity_logs")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "  {$c['Field']} ({$c['Type']})\n";
    }

    echo "\n=== activity_logs recent (5) ===\n";
    $rows = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "  " . json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
} catch (Exception $e) { $e->getMessage(); }
echo "Done\n";
