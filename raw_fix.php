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
     echo "Connected to DB\n";

     // Create vehicles
     $pdo->exec("CREATE TABLE IF NOT EXISTS `vehicles` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `targa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `marca` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `modello` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `immatricolazione_date` date DEFAULT NULL,
        `scadenza_assicurazione` date DEFAULT NULL,
        `scadenza_revisione` date DEFAULT NULL,
        `rottamazione_date` date DEFAULT NULL,
        `km_attuali` int(11) NOT NULL DEFAULT 0,
        `stato` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponibile',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `vehicles_targa_unique` (`targa`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
     echo "Table 'vehicles' checked/created\n";

     // Create vehicle_logs
     $pdo->exec("CREATE TABLE IF NOT EXISTS `vehicle_logs` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `vehicle_id` bigint(20) unsigned NOT NULL,
        `user_id` bigint(20) unsigned NOT NULL,
        `km_iniziali` int(11) NOT NULL,
        `km_finali` int(11) DEFAULT NULL,
        `assegnato_il` timestamp NOT NULL,
        `riconsegnato_il` timestamp NULL DEFAULT NULL,
        `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
     echo "Table 'vehicle_logs' checked/created\n";

     // Check area
     $stmt = $pdo->query("SELECT * FROM system_areas WHERE slug = 'autoparco'");
     $area = $stmt->fetch();
     if ($area) {
         echo "Area 'autoparco' status: " . ($area['is_active'] ? 'ACTIVE' : 'INACTIVE') . " (ID: {$area['id']})\n";
     } else {
         echo "Area 'autoparco' MISSING from system_areas. Adding it...\n";
         $pdo->exec("INSERT INTO system_areas (name, slug, description, is_active, sort_order) VALUES ('Autoparco', 'autoparco', 'Gestione Parco Macchine e Mezzi', 1, 99)");
         echo "Area 'autoparco' added.\n";
     }

} catch (\PDOException $e) {
     echo "PDO ERROR: " . $e->getMessage() . "\n";
}
