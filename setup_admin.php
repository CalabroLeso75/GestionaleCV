<?php

/**
 * Script per:
 * 1. Svuotare la tabella users e model_has_roles
 * 2. Creare l'utente admin Raffaele Bruno Cusano
 * 3. Assegnare il ruolo super-admin
 * 4. Collegare all'anagrafica interna
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

echo "<h1>🔧 Setup Utente Admin</h1>";

// ===== STEP 1: Clean tables =====
echo "<h2>1️⃣ Pulizia tabelle</h2>";

// Disable foreign key checks temporarily
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Clear role assignments
DB::table('model_has_roles')->delete();
echo "<p>✅ Tabella model_has_roles svuotata</p>";

// Clear users
DB::table('users')->delete();
echo "<p>✅ Tabella users svuotata</p>";

// Reset auto increment
DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

// Clear sessions
DB::table('sessions')->delete();
echo "<p>✅ Tabella sessions svuotata</p>";

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1');

// ===== STEP 2: Check if internal_employee_id column exists in users =====
echo "<h2>2️⃣ Verifica colonna collegamento anagrafica</h2>";

if (!Schema::hasColumn('users', 'internal_employee_id')) {
    Schema::table('users', function ($table) {
        $table->unsignedBigInteger('internal_employee_id')->nullable()->after('birth_country_id');
    });
    echo "<p>✅ Aggiunta colonna <strong>internal_employee_id</strong> alla tabella users</p>";
} else {
    echo "<p>ℹ️ Colonna internal_employee_id già presente</p>";
}

// ===== STEP 3: Look up birth country for USA in localizz_statoestero =====
echo "<h2>3️⃣ Ricerca dati geografici</h2>";

$usa = DB::table('localizz_statoestero')
    ->where('name', 'LIKE', '%Stati Uniti%')
    ->orWhere('name', 'LIKE', '%United States%')
    ->orWhere('name', 'LIKE', '%USA%')
    ->first();

if ($usa) {
    echo "<p>✅ Trovato: <strong>{$usa->name}</strong> (ID: {$usa->id})</p>";
    $birthCountryId = $usa->id;
} else {
    // Try broader search
    $usa = DB::table('localizz_statoestero')
        ->where('name', 'LIKE', '%America%')
        ->first();
    if ($usa) {
        echo "<p>✅ Trovato: <strong>{$usa->name}</strong> (ID: {$usa->id})</p>";
        $birthCountryId = $usa->id;
    } else {
        echo "<p>⚠️ Stati Uniti non trovato nella tabella localizz_statoestero. Verifica manualmente.</p>";
        $birthCountryId = null;
    }
}

// ===== STEP 4: Create Admin User =====
echo "<h2>4️⃣ Creazione utente admin</h2>";

$admin = User::create([
    'name' => 'Raffaele Bruno',
    'surname' => 'Cusano',
    'email' => 'raffaele.cusano@calabriaverde.eu',
    'password' => Hash::make('Admin2026!'), // Password temporanea
    'fiscal_code' => 'CSNRFL75M21Z404N',
    'birth_date' => '1975-08-21',
    'birth_city_id' => null, // Nato all'estero
    'birth_country_id' => $birthCountryId,
    'type' => 'internal',
    'status' => 'active', // Admin è direttamente attivo
    'email_verified_at' => now(), // Email già verificata
]);

echo "<p>✅ Utente creato con ID: <strong>{$admin->id}</strong></p>";
echo "<ul>";
echo "<li>Nome: {$admin->name} {$admin->surname}</li>";
echo "<li>Email: {$admin->email}</li>";
echo "<li>CF: {$admin->fiscal_code}</li>";
echo "<li>Nascita: {$admin->birth_date} - Estero ID: {$admin->birth_country_id}</li>";
echo "<li>Tipo: {$admin->type}</li>";
echo "<li>Status: <span style='color:green; font-weight:bold'>{$admin->status}</span></li>";
echo "</ul>";

// ===== STEP 5: Assign super-admin role =====
echo "<h2>5️⃣ Assegnazione ruolo</h2>";

try {
    $admin->assignRole('super-admin');
    echo "<p>✅ Ruolo <strong>super-admin</strong> assegnato!</p>";
} catch (\Exception $e) {
    echo "<p>❌ Errore: {$e->getMessage()}</p>";
}

// ===== STEP 6: Link to internal_employee_id (797) =====
echo "<h2>6️⃣ Collegamento anagrafica interna</h2>";

// Check if anag_persone has ID 797
$anag = DB::table('anag_persone')->where('id', 797)->first();
if ($anag) {
    echo "<p>ℹ️ Record anag_persone #797: <strong>{$anag->nome} {$anag->cognome}</strong></p>";
    // For now store the reference to the future internal_employee record
    // We'll set this after migration of anag_persone -> internal_employees
    DB::table('users')->where('id', $admin->id)->update(['internal_employee_id' => 797]);
    echo "<p>✅ Collegato a internal_employee_id = 797 (sarà valido dopo migrazione dati)</p>";
} else {
    echo "<p>⚠️ Record anag_persone con ID 797 non trovato.</p>";
}

// ===== SUMMARY =====
echo "<hr>";
echo "<h2>📋 Riepilogo</h2>";
echo "<div style='background:#e8f5e9; padding:20px; border-radius:8px'>";
echo "<p><strong>Utente Admin creato con successo!</strong></p>";
echo "<p>Email: <code>raffaele.cusano@calabriaverde.eu</code></p>";
echo "<p>Password temporanea: <code>Admin2026!</code></p>";
echo "<p>⚠️ <strong>Cambia la password al primo accesso!</strong></p>";
echo "</div>";

echo "<hr>";
echo "<p><a href='/GestionaleCV/login'>🔐 Vai al Login</a></p>";
echo "<p><a href='/GestionaleCV/migrate_anag_persone.php'>📊 Migra dati anag_persone → internal_employees</a></p>";
