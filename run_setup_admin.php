<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$DB = \Illuminate\Support\Facades\DB::getFacadeRoot();
$Schema = \Illuminate\Support\Facades\Schema::getFacadeRoot();
$Hash = \Illuminate\Support\Facades\Hash::getFacadeRoot();

echo "<h1>🔧 Setup Utente Admin</h1>";

// ===== STEP 1: Clean tables =====
echo "<h2>1️⃣ Pulizia tabelle</h2>";

try {
    $DB->statement('SET FOREIGN_KEY_CHECKS=0');
    $DB->table('model_has_roles')->delete();
    echo "<p>✅ model_has_roles svuotata</p>";
    $DB->table('users')->delete();
    echo "<p>✅ users svuotata</p>";
    $DB->statement('ALTER TABLE users AUTO_INCREMENT = 1');
    $DB->table('sessions')->delete();
    echo "<p>✅ sessions svuotata</p>";
    $DB->statement('SET FOREIGN_KEY_CHECKS=1');
} catch (\Throwable $e) {
    echo "<p style='color:red'>❌ Errore pulizia: {$e->getMessage()}</p>";
}

// ===== STEP 2: Add internal_employee_id column =====
echo "<h2>2️⃣ Verifica colonna collegamento</h2>";
try {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'internal_employee_id')) {
        \Illuminate\Support\Facades\Schema::table('users', function ($table) {
            $table->unsignedBigInteger('internal_employee_id')->nullable()->after('birth_country_id');
        });
        echo "<p>✅ Colonna internal_employee_id aggiunta</p>";
    } else {
        echo "<p>ℹ️ Colonna internal_employee_id già presente</p>";
    }
} catch (\Throwable $e) {
    echo "<p style='color:red'>❌ Errore colonna: {$e->getMessage()}</p>";
}

// ===== STEP 3: Find USA =====
echo "<h2>3️⃣ Ricerca paese nascita</h2>";
$birthCountryId = null;
try {
    $usa = $DB->table('localizz_statoestero')
        ->where('name', 'LIKE', '%Stati Uniti%')
        ->orWhere('name', 'LIKE', '%United States%')
        ->orWhere('name', 'LIKE', '%USA%')
        ->first();
    if ($usa) {
        echo "<p>✅ Trovato: <strong>{$usa->name}</strong> (ID: {$usa->id})</p>";
        $birthCountryId = $usa->id;
    } else {
        $all = $DB->table('localizz_statoestero')
            ->where('name', 'LIKE', '%Americ%')
            ->orWhere('name', 'LIKE', '%Unit%')
            ->get(['id', 'name']);
        echo "<p>⚠️ Non trovato 'Stati Uniti'. Risultati simili:</p><ul>";
        foreach ($all as $c) {
            echo "<li>ID:{$c->id} - {$c->name}</li>";
        }
        echo "</ul>";
    }
} catch (\Throwable $e) {
    echo "<p style='color:red'>❌ Errore ricerca: {$e->getMessage()}</p>";
}

// ===== STEP 4: Create Admin User =====
echo "<h2>4️⃣ Creazione utente admin</h2>";
try {
    $admin = \App\Models\User::create([
        'name' => 'Raffaele Bruno',
        'surname' => 'Cusano',
        'email' => 'raffaele.cusano@calabriaverde.eu',
        'password' => $Hash->make('Admin2026!'),
        'fiscal_code' => 'CSNRFL75M21Z404N',
        'birth_date' => '1975-08-21',
        'birth_city_id' => null,
        'birth_country_id' => $birthCountryId,
        'type' => 'internal',
        'status' => 'active',
        'email_verified_at' => now(),
    ]);

    echo "<p>✅ Utente creato con ID: <strong>{$admin->id}</strong></p>";
    echo "<ul>";
    echo "<li>Nome: {$admin->name} {$admin->surname}</li>";
    echo "<li>Email: {$admin->email}</li>";
    echo "<li>CF: {$admin->fiscal_code}</li>";
    echo "<li>Tipo: {$admin->type} | Status: <strong style='color:green'>{$admin->status}</strong></li>";
    echo "</ul>";

    // Assign role
    try {
        $admin->assignRole('super-admin');
        echo "<p>✅ Ruolo <strong>super-admin</strong> assegnato</p>";
    } catch (\Throwable $e) {
        echo "<p style='color:red'>❌ Errore ruolo: {$e->getMessage()}</p>";
    }

    // Link to anag_persone 797
    $anag = $DB->table('anag_persone')->where('id', 797)->first();
    if ($anag) {
        echo "<h2>6️⃣ Collegamento anagrafica</h2>";
        echo "<p>Record #797: <strong>{$anag->nome} {$anag->cognome}</strong></p>";
        $DB->table('users')->where('id', $admin->id)->update(['internal_employee_id' => 797]);
        echo "<p>✅ Collegato a internal_employee_id = 797</p>";
    } else {
        echo "<p>⚠️ Record anag_persone #797 non trovato</p>";
    }

    echo "<hr>";
    echo "<div style='background:#e8f5e9; padding:20px; border-radius:8px'>";
    echo "<p><strong>✅ Setup completato!</strong></p>";
    echo "<p>Email: <code>raffaele.cusano@calabriaverde.eu</code></p>";
    echo "<p>Password: <code>Admin2026!</code></p>";
    echo "<p>⚠️ <strong>Cambia la password al primo accesso!</strong></p>";
    echo "</div>";

} catch (\Throwable $e) {
    echo "<p style='color:red'>❌ Errore creazione: {$e->getMessage()}</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='/GestionaleCV/login'>🔐 Login</a> | ";
echo "<a href='/GestionaleCV/run_migration_v2.php'>📊 Migra anag_persone</a></p>";
