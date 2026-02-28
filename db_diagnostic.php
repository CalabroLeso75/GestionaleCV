<?php

/**
 * Script diagnostico per analizzare la struttura del database locale.
 * Mostra tutte le tabelle e le colonne rilevanti.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>🔍 Diagnostica Database</h1>";
echo "<p>Database: <strong>" . env('DB_DATABASE') . "</strong></p>";

// List all tables
$tables = DB::select('SHOW TABLES');
$dbName = env('DB_DATABASE');
$key = "Tables_in_{$dbName}";

echo "<h2>Tabelle presenti nel database:</h2>";
echo "<table border='1' cellpadding='6'>";
echo "<tr><th>#</th><th>Tabella</th><th>Righe</th><th>Colonne</th></tr>";

foreach ($tables as $i => $table) {
    $tableName = $table->$key;
    $count = DB::table($tableName)->count();
    $columns = Schema::getColumnListing($tableName);
    echo "<tr>";
    echo "<td>" . ($i + 1) . "</td>";
    echo "<td><strong>{$tableName}</strong></td>";
    echo "<td>{$count}</td>";
    echo "<td style='font-size:11px'>" . implode(', ', $columns) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show users table details
echo "<h2>Dettaglio tabella 'users':</h2>";
$users = DB::table('users')->get();
if ($users->count() > 0) {
    echo "<table border='1' cellpadding='4'>";
    $cols = array_keys((array) $users->first());
    echo "<tr>";
    foreach ($cols as $col) {
        echo "<th style='font-size:11px'>{$col}</th>";
    }
    echo "</tr>";
    foreach ($users as $u) {
        echo "<tr>";
        foreach ((array) $u as $val) {
            $display = is_null($val) ? '<em>NULL</em>' : htmlspecialchars(substr((string)$val, 0, 50));
            echo "<td style='font-size:11px'>{$display}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nessun utente trovato.</p>";
}

// Check if anag_persone exists
if (Schema::hasTable('anag_persone')) {
    echo "<h2>Tabella 'anag_persone' (prime 5 righe):</h2>";
    $rows = DB::table('anag_persone')->limit(5)->get();
    if ($rows->count() > 0) {
        echo "<p>Totale: <strong>" . DB::table('anag_persone')->count() . "</strong> righe</p>";
        echo "<table border='1' cellpadding='4'>";
        $cols = array_keys((array) $rows->first());
        echo "<tr>";
        foreach ($cols as $col) {
            echo "<th style='font-size:11px'>{$col}</th>";
        }
        echo "</tr>";
        foreach ($rows as $r) {
            echo "<tr>";
            foreach ((array) $r as $val) {
                $display = is_null($val) ? '<em>NULL</em>' : htmlspecialchars(substr((string)$val, 0, 60));
                echo "<td style='font-size:11px'>{$display}</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p>⚠️ Tabella 'anag_persone' non trovata nel database locale.</p>";
}

// Check Spatie permission tables
echo "<h2>Tabelle Permessi (Spatie):</h2>";
$permTables = ['roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions'];
foreach ($permTables as $pt) {
    if (Schema::hasTable($pt)) {
        $count = DB::table($pt)->count();
        echo "<p>✅ <strong>{$pt}</strong>: {$count} righe</p>";
        if ($count > 0 && in_array($pt, ['roles', 'permissions'])) {
            $items = DB::table($pt)->get();
            echo "<ul>";
            foreach ($items as $item) {
                echo "<li>{$item->name}</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p>❌ <strong>{$pt}</strong>: non esiste</p>";
    }
}
