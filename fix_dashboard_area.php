<?php
header('Content-Type: text/plain; charset=utf-8');
$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Add required_area column to dashboard_sections
try {
    $pdo->exec("ALTER TABLE dashboard_sections ADD COLUMN required_area VARCHAR(255) NULL AFTER required_role");
    echo "✓ Column required_area added\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ Column required_area already exists\n";
    } else {
        throw $e;
    }
}

// Set Risorse Umane card to require the Risorse Umane area
$pdo->exec("UPDATE dashboard_sections SET required_area = 'Risorse Umane' WHERE title = 'Risorse Umane'");
echo "✓ HR card now requires area 'Risorse Umane'\n";

// Verify
$rows = $pdo->query("SELECT id, title, required_role, required_area FROM dashboard_sections");
foreach ($rows as $r) {
    echo "  [{$r['id']}] {$r['title']} | role:{$r['required_role']} | area:{$r['required_area']}\n";
}
echo "\nDone!\n";
