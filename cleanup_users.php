<?php

/**
 * Script per pulire utenti con registrazione incompleta.
 * Elimina utenti con status='pending' e email non verificata.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "<h1>Pulizia Utenti Incompleti</h1>";

// Find users that are pending and have no verified email
$incompleteUsers = User::where('status', 'pending')
    ->whereNull('email_verified_at')
    ->get();

echo "<h2>Utenti con registrazione incompleta (pending + email non verificata):</h2>";
echo "<p>Trovati: <strong>" . $incompleteUsers->count() . "</strong></p>";

if ($incompleteUsers->count() > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Creato</th></tr>";
    foreach ($incompleteUsers as $u) {
        echo "<tr><td>{$u->id}</td><td>{$u->name} {$u->surname}</td><td>{$u->email}</td><td>{$u->created_at}</td></tr>";
    }
    echo "</table>";
}

// Also show verified-but-pending users (these are legitimate, waiting for admin)
$verifiedPending = User::where('status', 'pending')
    ->whereNotNull('email_verified_at')
    ->get();

echo "<h2>Utenti verificati in attesa di approvazione admin:</h2>";
echo "<p>Trovati: <strong>" . $verifiedPending->count() . "</strong></p>";

if ($verifiedPending->count() > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Verificato</th></tr>";
    foreach ($verifiedPending as $u) {
        echo "<tr><td>{$u->id}</td><td>{$u->name} {$u->surname}</td><td>{$u->email}</td><td>{$u->email_verified_at}</td></tr>";
    }
    echo "</table>";
}

// Delete incomplete users (only if ?delete=1 is passed)
if (isset($_GET['delete']) && $_GET['delete'] == '1') {
    $deleted = User::where('status', 'pending')
        ->whereNull('email_verified_at')
        ->delete();
    echo "<p style='color:green; font-weight:bold'>Eliminati $deleted utenti incompleti.</p>";
    echo "<p><a href='cleanup_users.php'>Aggiorna la pagina</a></p>";
} else {
    if ($incompleteUsers->count() > 0) {
        echo "<p><a href='cleanup_users.php?delete=1' style='color:red; font-weight:bold' onclick=\"return confirm('Sei sicuro? Questa azione è irreversibile.')\">🗑️ Elimina utenti incompleti</a></p>";
    }
}

echo "<hr>";
echo "<h2>Tutti gli utenti nel sistema:</h2>";
$allUsers = User::all();
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Status</th><th>Email Verificata</th><th>Tipo</th></tr>";
foreach ($allUsers as $u) {
    $statusColor = match($u->status) {
        'active' => 'green',
        'pending' => 'orange',
        'suspended' => 'red',
        default => 'gray'
    };
    echo "<tr>";
    echo "<td>{$u->id}</td>";
    echo "<td>{$u->name} {$u->surname}</td>";
    echo "<td>{$u->email}</td>";
    echo "<td style='color:{$statusColor}; font-weight:bold'>{$u->status}</td>";
    echo "<td>" . ($u->email_verified_at ? '✅ ' . $u->email_verified_at : '❌') . "</td>";
    echo "<td>{$u->type}</td>";
    echo "</tr>";
}
echo "</table>";
