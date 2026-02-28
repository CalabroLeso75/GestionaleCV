<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

$tables = $DB->select("SHOW TABLES LIKE 'rejected_users'");
if (count($tables) > 0) {
    echo "Tabella rejected_users esiste già.\n";
} else {
    $DB->statement("CREATE TABLE rejected_users (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        original_user_id BIGINT UNSIGNED NULL,
        name VARCHAR(255) NOT NULL,
        surname VARCHAR(255) NULL,
        email VARCHAR(255) NOT NULL,
        gender ENUM('male','female') NULL,
        fiscal_code VARCHAR(16) NULL,
        birth_date DATE NULL,
        birth_city_id BIGINT UNSIGNED NULL,
        birth_country_id BIGINT UNSIGNED NULL,
        password VARCHAR(255) NOT NULL,
        type VARCHAR(50) NULL,
        internal_employee_id BIGINT UNSIGNED NULL,
        rejected_by BIGINT UNSIGNED NOT NULL COMMENT 'Admin user ID who rejected',
        rejected_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        rejection_reason TEXT NULL,
        reintegrated_at TIMESTAMP NULL,
        reintegrated_by BIGINT UNSIGNED NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Tabella rejected_users creata!\n";
}

echo "\nStruttura:\n";
$cols = $DB->select("SHOW COLUMNS FROM rejected_users");
foreach ($cols as $c) {
    echo "  {$c->Field}: {$c->Type}\n";
}
