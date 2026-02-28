<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Create user_area_roles table
$tables = $DB->select("SHOW TABLES LIKE 'user_area_roles'");
if (count($tables) > 0) {
    echo "Tabella user_area_roles esiste già.\n";
} else {
    $DB->statement("CREATE TABLE user_area_roles (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        role VARCHAR(100) NOT NULL,
        area VARCHAR(255) NOT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Tabella user_area_roles creata!\n";
}

echo "\nStruttura:\n";
$cols = $DB->select("SHOW COLUMNS FROM user_area_roles");
foreach ($cols as $c) echo "  {$c->Field}: {$c->Type}\n";

echo "\n\nDati dipendenti interni:\n";
$emps = $DB->select("SELECT id, first_name, last_name, tax_code, position FROM internal_employees LIMIT 5");
foreach ($emps as $e) echo "  ID:{$e->id} | {$e->first_name} {$e->last_name} | CF:{$e->tax_code} | Pos:{$e->position}\n";

echo "\nDati dipendenti esterni:\n";
$emps2 = $DB->select("SELECT id, first_name, last_name, tax_code, job_title FROM external_employees LIMIT 5");
foreach ($emps2 as $e) echo "  ID:{$e->id} | {$e->first_name} {$e->last_name} | CF:{$e->tax_code} | Job:{$e->job_title}\n";
