<?php
/**
 * HostingControlCenter - Pannello di controllo per l'hosting
 * Accesso SOLO via URL protetto con token segreto
 * URL: /GestionaleCV/public/hcc.php?token=YOUR_TOKEN
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// =============================================
// SICUREZZA: cambia questo token con uno tuo!
// =============================================
$SECRET_TOKEN = 'CV2026admin!secureToken';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
if ($token !== $SECRET_TOKEN) {
    http_response_code(403);
    die('<h1>403 Forbidden</h1>');
}

// Esegui comando Artisan se richiesto
$artisan_output = '';
$artisan_cmd = $_POST['cmd'] ?? '';
$allowed_cmds = [
    'migrate'          => 'php artisan migrate --force',
    'migrate:status'   => 'migrate:status',
    'cache:clear'      => 'cache:clear',
    'config:clear'     => 'config:clear',
    'view:clear'       => 'view:clear',
    'route:clear'      => 'route:clear',
    'optimize:clear'   => 'optimize:clear',
    'optimize'         => 'optimize',
    'queue:restart'    => 'queue:restart',
    'storage:link'     => 'storage:link',
];

if ($artisan_cmd && isset($allowed_cmds[$artisan_cmd])) {
    try {
        ob_start();
        if ($artisan_cmd === 'migrate') {
            Artisan::call('migrate', ['--force' => true]);
        } else {
            Artisan::call($artisan_cmd);
        }
        $artisan_output = Artisan::output();
        if (empty(trim($artisan_output))) {
            $artisan_output = ob_get_clean() ?: '✅ Completato senza output.';
        } else {
            ob_end_clean();
        }
    } catch (Exception $e) {
        $artisan_output = '❌ ERRORE: ' . $e->getMessage();
    }
}

// Info di sistema
$php_version = PHP_VERSION;
$laravel_version = app()->version();
$app_env = app()->environment();
$db_ok = false;
$db_error = '';
$tables_count = 0;
try {
    DB::connection()->getPdo();
    $db_ok = true;
    $tables_count = count(DB::select('SHOW TABLES'));
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

// Log file
$log_file = storage_path('logs/laravel.log');
$log_content = '';
if (file_exists($log_file)) {
    $lines = file($log_file);
    $last_lines = array_slice($lines, -80);
    $log_content = implode('', $last_lines);
}

// Verifica file critici usando percorsi assoluti basati su __DIR__
$critical_files = [
    '.env'                => base_path('.env'),
    'public/.htaccess'    => __DIR__ . '/.htaccess',
    'public/index.php'    => __DIR__ . '/index.php',
    'storage/ (writable)' => storage_path(),
    'vendor/autoload.php' => base_path('vendor/autoload.php'),
];

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🛠️ Hosting Control Center — GestionaleCV</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', monospace; background: #0f172a; color: #e2e8f0; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #1e3a8a, #0f172a); padding: 20px 30px; border-bottom: 1px solid #334155; }
        .header h1 { font-size: 1.4em; color: #60a5fa; }
        .header p { color: #94a3b8; font-size: 0.85em; margin-top: 4px; }
        .container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; padding: 20px 30px; max-width: 1400px; margin: 0 auto; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 20px; }
        .card h2 { color: #60a5fa; font-size: 1em; margin-bottom: 15px; border-bottom: 1px solid #334155; padding-bottom: 8px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.75em; font-weight: bold; }
        .badge.ok { background: #064e3b; color: #34d399; }
        .badge.err { background: #7f1d1d; color: #f87171; }
        .badge.warn { background: #78350f; color: #fbbf24; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #1e293b; font-size: 0.85em; }
        .info-row:last-child { border-bottom: none; }
        .btn { display: inline-block; padding: 7px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.8em; font-weight: bold; margin: 3px; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-green { background: #065f46; color: #34d399; }
        .btn-blue { background: #1e3a8a; color: #93c5fd; }
        .btn-red { background: #7f1d1d; color: #fca5a5; }
        .btn-orange { background: #78350f; color: #fbbf24; }
        .btn-gray { background: #334155; color: #94a3b8; }
        .output-box { background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 12px; font-family: monospace; font-size: 0.78em; max-height: 180px; overflow-y: auto; color: #86efac; white-space: pre-wrap; margin-top: 10px; }
        .log-box { background: #0f172a; border: 1px solid #334155; border-radius: 6px; padding: 12px; font-family: monospace; font-size: 0.72em; height: 300px; overflow-y: auto; color: #94a3b8; white-space: pre-wrap; }
        .log-box .err-line { color: #f87171; }
        .full-width { grid-column: 1 / -1; }
        .env-key { color: #60a5fa; }
        .env-val { color: #86efac; }
        form { display: inline; }
    </style>
</head>
<body>

<div class="header">
    <h1>🛠️ Hosting Control Center — GestionaleCV</h1>
    <p>Ambiente: <strong><?= htmlspecialchars($app_env) ?></strong> &nbsp;|&nbsp;
       PHP: <strong><?= $php_version ?></strong> &nbsp;|&nbsp;
       Laravel: <strong><?= $laravel_version ?></strong> &nbsp;|&nbsp;
       DB: <?= $db_ok ? '<span class="badge ok">✅ Connesso ('.$tables_count.' tabelle)</span>' : '<span class="badge err">❌ '.$db_error.'</span>' ?>
    </p>
</div>

<div class="container">

    <!-- FILE CRITICI -->
    <div class="card">
        <h2>📁 File & Permessi Critici</h2>
        <?php foreach ($critical_files as $name => $path): ?>
        <div class="info-row">
            <span><?= htmlspecialchars($name) ?></span>
            <?php if (is_dir($path)): ?>
                <span class="badge <?= is_writable($path) ? 'ok' : 'err' ?>"><?= is_writable($path) ? '✅ Scrivibile' : '❌ Non scrivibile' ?></span>
            <?php else: ?>
                <span class="badge <?= file_exists($path) ? 'ok' : 'err' ?>"><?= file_exists($path) ? '✅ Esiste' : '❌ Mancante' ?></span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- INFO AMBIENTE -->
    <div class="card">
        <h2>⚙️ Configurazione App</h2>
        <?php
        $configs = [
            'APP_NAME'   => config('app.name'),
            'APP_ENV'    => config('app.env'),
            'APP_URL'    => config('app.url'),
            'APP_DEBUG'  => config('app.debug') ? '⚠️ TRUE' : 'false',
            'DB_HOST'    => config('database.connections.mysql.host'),
            'DB_DATABASE'=> config('database.connections.mysql.database'),
            'DB_USERNAME'=> config('database.connections.mysql.username'),
            'CACHE_STORE'=> config('cache.default'),
            'SESSION'    => config('session.driver'),
        ];
        foreach ($configs as $k => $v):
        ?>
        <div class="info-row">
            <span class="env-key"><?= $k ?></span>
            <span class="env-val"><?= htmlspecialchars($v) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- COMANDI ARTISAN -->
    <div class="card">
        <h2>🚀 Comandi Artisan</h2>
        <p style="font-size:0.75em; color:#94a3b8; margin-bottom:10px;">Clicca un pulsante per eseguire il comando sul server.</p>

        <?php
        $cmd_btns = [
            'migrate'        => ['btn-green',  '🗄️ Migrate (--force)'],
            'migrate:status' => ['btn-gray',   '📋 Migrate Status'],
            'cache:clear'    => ['btn-blue',   '🧹 Cache Clear'],
            'config:clear'   => ['btn-blue',   '⚙️ Config Clear'],
            'view:clear'     => ['btn-blue',   '🖼️ View Clear'],
            'route:clear'    => ['btn-blue',   '🛣️ Route Clear'],
            'optimize:clear' => ['btn-orange', '🔄 Optimize Clear'],
            'optimize'       => ['btn-green',  '⚡ Optimize'],
            'storage:link'   => ['btn-gray',   '🔗 Storage Link'],
        ];
        foreach ($cmd_btns as $cmd => [$cls, $label]):
        ?>
        <form method="POST" action="?token=<?= htmlspecialchars($SECRET_TOKEN) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($SECRET_TOKEN) ?>">
            <input type="hidden" name="cmd" value="<?= $cmd ?>">
            <button type="submit" class="btn <?= $cls ?>"><?= $label ?></button>
        </form>
        <?php endforeach; ?>

        <?php if ($artisan_output): ?>
        <div class="output-box"><?= htmlspecialchars($artisan_output) ?></div>
        <?php endif; ?>
    </div>

    <!-- TABELLE DB -->
    <div class="card">
        <h2>🗃️ Database — Tabelle</h2>
        <?php if ($db_ok):
            $tables = DB::select('SHOW TABLES');
            $key = 'Tables_in_' . config('database.connections.mysql.database');
            foreach ($tables as $t):
                $tname = $t->$key;
                try {
                    $count = DB::table($tname)->count();
                } catch(Exception $e) { $count = '?'; }
        ?>
        <div class="info-row">
            <span style="font-family:monospace;font-size:0.82em"><?= htmlspecialchars($tname) ?></span>
            <span class="badge ok"><?= $count ?> righe</span>
        </div>
        <?php endforeach;
        else: ?>
        <p class="badge err">❌ Nessuna connessione DB</p>
        <?php endif; ?>
    </div>

    <!-- LOG -->
    <div class="card full-width">
        <h2>📋 Laravel Log (ultime 80 righe) &nbsp;
            <a href="?token=<?= htmlspecialchars($SECRET_TOKEN) ?>&clear_log=1" style="font-size:0.8em; color:#f87171; text-decoration:none;">🗑️ Svuota log</a>
        </h2>
        <?php if ($_GET['clear_log'] ?? false):
            file_put_contents($log_file, '');
            echo '<p style="color:#34d399">✅ Log svuotato.</p>';
        endif; ?>
        <div class="log-box">
            <?php
            $lines = explode("\n", $log_content);
            foreach ($lines as $line) {
                if (str_contains($line, 'ERROR') || str_contains($line, 'CRITICAL') || str_contains($line, 'Exception')) {
                    echo '<span class="err-line">' . htmlspecialchars($line) . '</span>' . "\n";
                } else {
                    echo htmlspecialchars($line) . "\n";
                }
            }
            ?>
        </div>
    </div>

</div>

</body>
</html>
