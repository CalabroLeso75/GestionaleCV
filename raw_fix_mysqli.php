<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'gestionale_cv';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database: $db\n";

// Show existing tables
$result = $conn->query("SHOW TABLES");
echo "Existing Tables:\n";
while($row = $result->fetch_array()) {
    echo "- " . $row[0] . "\n";
}

// Ensure vehicles table
$sql = "CREATE TABLE IF NOT EXISTS `vehicles` (
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
  `stato` enum('disponibile','in uso','manutenzione','fuori servizio') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponibile',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_targa_unique` (`targa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table 'vehicles' created successfully or already exists.\n";
} else {
    echo "Error creating table 'vehicles': " . $conn->error . "\n";
}

// Ensure vehicle_logs table
$sql = "CREATE TABLE IF NOT EXISTS `vehicle_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `km_iniziali` int(11) NOT NULL,
  `km_finali` int(11) DEFAULT NULL,
  `assegnato_il` timestamp NOT NULL,
  `riconsegnato_il` timestamp NULL DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($conn->query($sql) === TRUE) {
    echo "Table 'vehicle_logs' created successfully or already exists.\n";
} else {
    echo "Error creating table 'vehicle_logs': " . $conn->error . "\n";
}

$conn->close();
?>
