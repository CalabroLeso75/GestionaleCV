<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Check if table exists
$tables = $DB->select("SHOW TABLES LIKE 'dashboard_sections'");
if (count($tables) > 0) {
    echo "Tabella dashboard_sections esiste già.\n";
} else {
    $DB->statement("CREATE TABLE dashboard_sections (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description VARCHAR(255) NULL,
        icon VARCHAR(10) DEFAULT '📁',
        route VARCHAR(255) NULL,
        color VARCHAR(20) DEFAULT '#0066cc',
        required_role VARCHAR(255) NULL,
        is_active TINYINT(1) DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Tabella dashboard_sections creata!\n";
}

// Show table structure
echo "\nStruttura:\n";
$cols = $DB->select("SHOW COLUMNS FROM dashboard_sections");
foreach ($cols as $c) {
    echo "  {$c->Field}: {$c->Type}\n";
}
