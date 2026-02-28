<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();

echo "<h1>📊 Migrazione anag_persone → internal_employees</h1>";

$existingCount = $DB->table('internal_employees')->count();

if ($existingCount > 0 && !isset($_GET['force'])) {
    echo "<p>⚠️ internal_employees contiene già <strong>{$existingCount}</strong> record.</p>";
    echo "<p><a href='?force=1' onclick=\"return confirm('Svuotare e rimigrare?')\">🔄 Forza re-migrazione</a></p>";
} else {
    if ($existingCount > 0) {
        $DB->statement('SET FOREIGN_KEY_CHECKS=0');
        $DB->table('internal_employees')->delete();
        $DB->statement('ALTER TABLE internal_employees AUTO_INCREMENT = 1');
        $DB->statement('SET FOREIGN_KEY_CHECKS=1');
        echo "<p>🗑️ Tabella svuotata.</p>";
    }
    
    $source = $DB->table('anag_persone')->get();
    $total = $source->count();
    echo "<p>Trovati <strong>{$total}</strong> record in anag_persone.</p>";
    
    $migrated = 0;
    $errors = 0;
    
    foreach ($source as $row) {
        try {
            $status = match($row->stato_rapporto) {
                'operativo' => 'active',
                'sospeso' => 'suspended',
                'cessato' => 'terminated',
                'in attesa' => 'pending',
                default => 'active',
            };
            
            $gender = match($row->genere) {
                'uomo' => 'male',
                'donna' => 'female',
                default => null,
            };
            
            $empType = match($row->tipo_personale) {
                'interno' => 'internal',
                'esterno' => 'external',
                default => $row->tipo_personale ?? 'unknown',
            };
            
            $DB->table('internal_employees')->insert([
                'id' => $row->id,
                'user_id' => null,
                'first_name' => $row->nome,
                'last_name' => $row->cognome,
                'tax_code' => $row->codice_fiscale ?? 'N/D',
                'birth_date' => $row->data_nascita ?? '1900-01-01',
                'birth_place' => $row->luogo_nascita_testo ?? '',
                'gender' => $gender,
                'badge_number' => $row->matricola,
                'position' => $row->ccnl_posizione,
                'employee_type' => in_array($empType, ['internal', 'external']) ? $empType : 'internal',
                'status' => $status,
                'email' => $row->email_aziendale,
                'personal_email' => $row->email_personale,
                'phone' => $row->telefono_aziendale,
                'personal_phone' => $row->telefono_personale,
                'is_aib_qualified' => $row->requisiti_aib ? 1 : 0,
                'is_emergency_available' => $row->disponibile_emergenze ? 1 : 0,
                'operational_roles' => $row->ruoli_operativi,
                'organization_id' => $row->organizzazione_id,
                'contract_id' => $row->contratto_id,
                'level_id' => $row->ccnl_livello_id,
                'location_id' => $row->distretto_id ?? $row->postazione_id,
                'notes' => $row->note,
                'created_at' => $row->creato_il,
                'updated_at' => $row->aggiornato_il,
            ]);
            $migrated++;
        } catch (\Throwable $e) {
            $errors++;
            if ($errors <= 5) {
                echo "<p style='color:orange'>⚠️ ID {$row->id} ({$row->nome} {$row->cognome}): {$e->getMessage()}</p>";
            }
        }
    }
    
    echo "<div style='background:#e8f5e9; padding:20px; border-radius:8px; margin-top:20px;'>";
    echo "<h2>✅ Migrazione Completata!</h2>";
    echo "<p><strong>Migrati:</strong> {$migrated} / {$total}</p>";
    if ($errors > 0) {
        echo "<p style='color:orange'><strong>Errori:</strong> {$errors}</p>";
    }
    echo "</div>";
}

// Show first 10
echo "<hr><h2>Primi 10 record</h2>";
$sample = $DB->table('internal_employees')->limit(10)->get();
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Nome</th><th>Cognome</th><th>CF</th><th>Nascita</th><th>Luogo</th><th>Matricola</th><th>Status</th></tr>";
foreach ($sample as $r) {
    echo "<tr><td>{$r->id}</td><td>{$r->first_name}</td><td>{$r->last_name}</td><td>{$r->tax_code}</td>";
    echo "<td>{$r->birth_date}</td><td>{$r->birth_place}</td><td>{$r->badge_number}</td><td>{$r->status}</td></tr>";
}
echo "</table>";
echo "<p><em>Totale: " . $DB->table('internal_employees')->count() . " record</em></p>";

echo "<hr>";
echo "<p><a href='/GestionaleCV/login'>🔐 Login</a> | <a href='/GestionaleCV/admin/smtp'>⚙️ SMTP</a></p>";
