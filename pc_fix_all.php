<?php
$conn = new mysqli('127.0.0.1', 'root', '', 'gestionale_cv');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

echo "Initial check:\n";

// Ensure tables
$tables = [
    "CREATE TABLE IF NOT EXISTS `vehicles` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `targa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `marca` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `modello` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `immatricolazione_date` date DEFAULT NULL,
        `scadenza_assicurazione` date DEFAULT NULL,
        `scadenza_revisione` date DEFAULT NULL,
        `rottamazione_date` date DEFAULT NULL,
        `km_attuali` int(11) NOT NULL DEFAULT '0',
        `stato` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponibile',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `vehicles_targa_unique` (`targa`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    "CREATE TABLE IF NOT EXISTS `vehicle_logs` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `vehicle_id` bigint(20) unsigned NOT NULL,
        `user_id` bigint(20) unsigned NOT NULL,
        `km_iniziali` int(11) NOT NULL,
        `km_finali` int(11) DEFAULT NULL,
        `assegnato_il` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `riconsegnato_il` timestamp NULL DEFAULT NULL,
        `note` text COLLATE utf8mb4_unicode_ci,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($tables as $sql) {
    if ($conn->query($sql)) echo "Table processed successfully.\n";
    else echo "Error: " . $conn->error . "\n";
}

// Ensure system_areas
$slugs = [
    'soup' => 'S.O.U.P. - Sala Operativa Unificata Permanente',
    'cop-cosenza' => 'C.O.P. Cosenza',
    'cop-catanzaro' => 'C.O.P. Catanzaro',
    'cop-crotone' => 'C.O.P. Crotone',
    'cop-vibo-valentia' => 'C.O.P. Vibo Valentia',
    'cop-reggio-calabria' => 'C.O.P. Reggio Calabria',
    'antincendio-boschivo' => 'Antincendio Boschivo (AIB)',
    'emergenze-pc' => 'Gestione Emergenze Protezione Civile',
    'mezzi-aerei' => 'Gestione Mezzi Aerei Regionali',
    'squadre-aib-pc' => 'Gestione Squadre AIB',
    'mezzi-terra' => 'Gestione Mezzi di Terra',
    'utenze-aziendali' => 'Gestione Utenze Aziendali',
    'turnazioni-aib' => 'Gestione Turnazioni AIB',
    'autoparco' => 'Gestione Autoparco e Mezzi'
];

foreach ($slugs as $slug => $name) {
    $res = $conn->query("SELECT id FROM system_areas WHERE slug = '$slug'");
    if ($res->num_rows == 0) {
        $conn->query("INSERT INTO system_areas (name, slug, is_active, sort_order) VALUES ('$name', '$slug', 1, 10)");
        echo "Inserted missing area: $slug\n";
    } else {
        $conn->query("UPDATE system_areas SET is_active = 1 WHERE slug = '$slug'");
        echo "Updated area status: $slug\n";
    }
}

// Ensure permission for user 1
$res = $conn->query("SELECT id FROM user_area_roles WHERE user_id = 1 AND (area = 'autoparco' OR area = 'Gestione Autoparco e Mezzi')");
if ($res->num_rows == 0) {
    $conn->query("INSERT INTO user_area_roles (user_id, area, role, privilege_level) VALUES (1, 'autoparco', 'Super Admin', 1)");
    echo "Assigned autoparco permission to user 1.\n";
}

$conn->close();
echo "Done.\n";
