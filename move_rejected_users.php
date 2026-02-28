<?php
header('Content-Type: text/plain; charset=utf-8');
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Find rejected users still in users table
$rejected = $DB->select("SELECT * FROM users WHERE status = 'rejected'");
echo "Utenti con status 'rejected': " . count($rejected) . "\n";

foreach ($rejected as $u) {
    echo "\n--- Utente ID {$u->id}: {$u->name} {$u->surname} ({$u->email}) ---\n";
    
    // Check if already in rejected_users
    $exists = $DB->select("SELECT id FROM rejected_users WHERE email = ?", [$u->email]);
    if (count($exists) > 0) {
        echo "  Già archiviato, salto.\n";
        continue;
    }
    
    // Archive to rejected_users
    $DB->insert("INSERT INTO rejected_users 
        (original_user_id, name, surname, email, gender, fiscal_code, birth_date, 
         birth_city_id, birth_country_id, password, type, internal_employee_id, 
         rejected_by, rejected_at, rejection_reason, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW(), NOW())", [
        $u->id,
        $u->name,
        $u->surname,
        $u->email,
        $u->gender,
        $u->fiscal_code ?? null,
        $u->birth_date ?? null,
        $u->birth_city_id ?? null,
        $u->birth_country_id ?? null,
        $u->password,
        $u->type ?? null,
        $u->internal_employee_id ?? null,
        1, // Admin user ID
        'Rifiutato durante test iniziale'
    ]);
    
    // Delete from users
    $DB->delete("DELETE FROM users WHERE id = ?", [$u->id]);
    echo "  ✅ Archiviato e rimosso da users.\n";
}

// Show final state
echo "\n\n=== Archivio rifiutati ===\n";
$archived = $DB->select("SELECT * FROM rejected_users WHERE reintegrated_at IS NULL");
foreach ($archived as $a) {
    echo "  ID:{$a->id} | {$a->name} {$a->surname} | {$a->email} | Rifiutato: {$a->rejected_at}\n";
}

echo "\n=== Utenti attivi ===\n";
$active = $DB->select("SELECT id, name, surname, email, status FROM users");
foreach ($active as $u) {
    echo "  ID:{$u->id} | {$u->name} {$u->surname} | {$u->email} | {$u->status}\n";
}
