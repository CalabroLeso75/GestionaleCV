<?php
header('Content-Type: text/plain; charset=utf-8');

$pdo = new PDO('mysql:host=localhost;dbname=gestionale_cv', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create system_areas table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS system_areas (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        description TEXT NULL,
        is_active BOOLEAN NOT NULL DEFAULT TRUE,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ Table system_areas created\n";

// Populate with existing areas
$areas = [
    ['Risorse Umane', 'risorse-umane', 'Gestione del personale interno ed esterno', 1],
    ['Forestazione', 'forestazione', 'Gestione forestale e rimboschimento', 2],
    ['Autoparco', 'autoparco', 'Gestione veicoli e mezzi', 3],
    ['Magazzino', 'magazzino', 'Gestione materiali e forniture', 4],
    ['Sala Operativa Unificata Permanente', 'soup', 'Sala operativa centrale', 5],
    ['Centro Operativo Provinciale Area Nord', 'cop-nord', 'COP zona nord', 6],
    ['Centro Operativo Provinciale Area Centro', 'cop-centro', 'COP zona centro', 7],
    ['Centro Operativo Provinciale Area Sud', 'cop-sud', 'COP zona sud', 8],
    ['Vivai', 'vivai', 'Gestione vivai forestali', 9],
    ['Antincendio Boschivo', 'antincendio-boschivo', 'Prevenzione e lotta incendi boschivi', 10],
    ['Supporto Protezione Civile', 'protezione-civile', 'Supporto attività di protezione civile', 11],
];

$stmt = $pdo->prepare("
    INSERT INTO system_areas (name, slug, description, sort_order) 
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), sort_order = VALUES(sort_order)
");

foreach ($areas as $a) {
    $stmt->execute($a);
    echo "  ✓ {$a[0]}\n";
}

echo "\n✓ All areas inserted (" . count($areas) . ")\n";

// Verify
echo "\n=== Verifica ===\n";
$rows = $pdo->query("SELECT id, name, slug, is_active, sort_order FROM system_areas ORDER BY sort_order");
foreach ($rows as $r) {
    $active = $r['is_active'] ? '✅' : '❌';
    echo "  {$active} [{$r['id']}] {$r['name']} ({$r['slug']})\n";
}
