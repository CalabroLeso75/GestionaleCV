<?php

/**
 * Migrazione dati da anag_persone a internal_employees.
 * Mappa le colonne italiane alle colonne inglesi del nuovo schema.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>📊 Migrazione anag_persone → internal_employees</h1>";

// Check tables exist
if (!Schema::hasTable('anag_persone')) {
    die("<p style='color:red'>❌ Tabella anag_persone non trovata!</p>");
}

if (!Schema::hasTable('internal_employees')) {
    die("<p style='color:red'>❌ Tabella internal_employees non trovata!</p>");
}

// Check if already migrated
$existingCount = DB::table('internal_employees')->count();
if ($existingCount > 0 && !isset($_GET['force'])) {
    echo "<p>⚠️ La tabella internal_employees contiene già <strong>{$existingCount}</strong> record.</p>";
    echo "<p><a href='?force=1' onclick=\"return confirm('Vuoi svuotare e rimigrare?')\">🔄 Forza re-migrazione (svuota e rimigra)</a></p>";
    echo "<hr>";
}

if ($existingCount > 0 && isset($_GET['force'])) {
    DB::table('internal_employees')->truncate();
    echo "<p>🗑️ Tabella internal_employees svuotata.</p>";
    $existingCount = 0;
}

if ($existingCount === 0) {
    $source = DB::table('anag_persone')->get();
    $total = $source->count();
    echo "<p>Trovati <strong>{$total}</strong> record in anag_persone.</p>";

    $migrated = 0;
    $errors = 0;

    foreach ($source as $row) {
        try {
            // Map status
            $status = match($row->stato_rapporto) {
                'operativo' => 'active',
                'sospeso' => 'suspended',
                'cessato' => 'terminated',
                default => $row->stato_rapporto ?? 'unknown',
            };

            // Map gender
            $gender = match($row->genere) {
                'uomo' => 'M',
                'donna' => 'F',
                default => $row->genere,
            };

            // Map employee type
            $empType = match($row->tipo_personale) {
                'interno' => 'internal',
                'esterno' => 'external',
                default => $row->tipo_personale ?? 'unknown',
            };

            // Build birth place text
            $birthPlace = $row->luogo_nascita_testo ?? '';

            DB::table('internal_employees')->insert([
                'id' => $row->id, // Keep same ID for references
                'user_id' => null, // Will link later when user registers
                'first_name' => $row->nome,
                'last_name' => $row->cognome,
                'tax_code' => $row->codice_fiscale,
                'birth_date' => $row->data_nascita,
                'birth_place' => $birthPlace,
                'gender' => $gender,
                'badge_number' => $row->matricola,
                'position' => $row->ccnl_posizione,
                'employee_type' => $empType,
                'status' => $status,
                'email' => $row->email_aziendale,
                'personal_email' => $row->email_personale,
                'phone' => $row->telefono_aziendale,
                'personal_phone' => $row->telefono_personale,
                'is_aib_qualified' => $row->requisiti_aib ? true : false,
                'is_emergency_available' => $row->disponibile_emergenze ? true : false,
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
        } catch (\Exception $e) {
            $errors++;
            if ($errors <= 5) {
                echo "<p style='color:orange'>⚠️ Errore riga ID {$row->id} ({$row->nome} {$row->cognome}): {$e->getMessage()}</p>";
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

// Show summary
echo "<hr>";
echo "<h2>Riepilogo internal_employees</h2>";
$finalCount = DB::table('internal_employees')->count();
echo "<p>Totale record: <strong>{$finalCount}</strong></p>";

// Show first 10
$sample = DB::table('internal_employees')->limit(10)->get();
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Nome</th><th>Cognome</th><th>CF</th><th>Nascita</th><th>Luogo</th><th>Matricola</th><th>Status</th></tr>";
foreach ($sample as $r) {
    echo "<tr>";
    echo "<td>{$r->id}</td>";
    echo "<td>{$r->first_name}</td>";
    echo "<td>{$r->last_name}</td>";
    echo "<td>{$r->tax_code}</td>";
    echo "<td>{$r->birth_date}</td>";
    echo "<td>{$r->birth_place}</td>";
    echo "<td>{$r->badge_number}</td>";
    echo "<td>{$r->status}</td>";
    echo "</tr>";
}
echo "</table>";
echo "<p><em>... (visualizzati i primi 10)</em></p>";

echo "<hr>";
echo "<p><a href='/GestionaleCV/admin/smtp'>⚙️ Impostazioni SMTP</a> | ";
echo "<a href='/GestionaleCV/cleanup_users.php'>👥 Gestione Utenti</a> | ";
echo "<a href='/GestionaleCV/login'>🔐 Login</a></p>";
