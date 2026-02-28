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
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     $out = "LOGGING PHP OUTPUT\n";
     
     // Tables
     $pdo->exec("CREATE TABLE IF NOT EXISTS `vehicles` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `targa` varchar(255) NOT NULL,
        `marca` varchar(255) NOT NULL,
        `modello` varchar(255) NOT NULL,
        `tipo` varchar(255) NOT NULL,
        `immatricolazione_date` date DEFAULT NULL,
        `scadenza_assicurazione` date DEFAULT NULL,
        `scadenza_revisione` date DEFAULT NULL,
        `rottamazione_date` date DEFAULT NULL,
        `km_attuali` int(11) NOT NULL DEFAULT 0,
        `stato` varchar(255) NOT NULL DEFAULT 'disponibile',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `vehicles_targa_unique` (`targa`)
      )");
     $out .= "Table 'vehicles' checked.\n";

     $pdo->exec("CREATE TABLE IF NOT EXISTS `vehicle_logs` (
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `vehicle_id` bigint(20) unsigned NOT NULL,
        `user_id` bigint(20) unsigned NOT NULL,
        `km_iniziali` int(11) NOT NULL,
        `km_finali` int(11) DEFAULT NULL,
        `assegnato_il` timestamp NOT NULL,
        `riconsegnato_il` timestamp NULL DEFAULT NULL,
        `note` text DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
      )");
     $out .= "Table 'vehicle_logs' checked.\n";

     // Add autoparco to system_areas if missing
     $stmt = $pdo->query("SELECT * FROM system_areas WHERE slug = 'autoparco'");
     $area = $stmt->fetch();
     if (!$area) {
         $pdo->exec("INSERT INTO system_areas (name, slug, description, is_active, sort_order) 
                     VALUES ('Autoparco', 'autoparco', 'Gestione Parco Macchine e Mezzi', 1, 99)");
         $out .= "Area 'autoparco' added to system_areas.\n";
     } else {
         $out .= "Area 'autoparco' already exists in system_areas.\n";
     }

     // Give user ID 1 permission if missing
     $stmt = $pdo->query("SELECT * FROM user_area_roles WHERE user_id = 1 AND area = 'autoparco'");
     if (!$stmt->fetch()) {
         $pdo->exec("INSERT INTO user_area_roles (user_id, area, role, privilege_level) 
                     VALUES (1, 'autoparco', 'Responsabile Autoparco', 1)");
         $out .= "User 1 given permission for 'autoparco'.\n";
     } else {
         $out .= "User 1 already has permission for 'autoparco'.\n";
     }

     file_put_contents('raw_fix_result.txt', $out);
     echo "DONE";

} catch (\Exception $e) {
     file_put_contents('raw_fix_result.txt', "PDO ERROR: " . $e->getMessage());
     echo "ERROR";
}
