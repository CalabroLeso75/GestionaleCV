<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>🔧 Magazzino DB Setup (Raw SQL)</h2>";
echo "<style>body{font-family:monospace;padding:20px;font-size:14px} .ok{color:green} .err{color:red} pre{background:#f5f5f5;padding:8px;border-radius:4px}</style>";

function trySQL($label, $sql) {
    try {
        DB::statement($sql);
        echo "<p class='ok'>✅ $label</p>";
    } catch (Exception $e) {
        echo "<p class='err'>❌ $label: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// warehouse_products – add missing columns if any
$cols = DB::select("SHOW COLUMNS FROM warehouse_products");
$existingCols = array_column(array_map('get_object_vars', $cols), 'Field');

if (!in_array('barcode', $existingCols))
    trySQL('warehouse_products.barcode', "ALTER TABLE warehouse_products ADD COLUMN barcode VARCHAR(255) NULL UNIQUE");
if (!in_array('brand', $existingCols))
    trySQL('warehouse_products.brand', "ALTER TABLE warehouse_products ADD COLUMN brand VARCHAR(255) NULL");
if (!in_array('category', $existingCols))
    trySQL('warehouse_products.category', "ALTER TABLE warehouse_products ADD COLUMN category VARCHAR(255) NULL");
if (!in_array('unit_of_measure', $existingCols))
    trySQL('warehouse_products.unit_of_measure', "ALTER TABLE warehouse_products ADD COLUMN unit_of_measure VARCHAR(50) NOT NULL DEFAULT 'pz'");
if (!in_array('is_inventariable', $existingCols))
    trySQL('warehouse_products.is_inventariable', "ALTER TABLE warehouse_products ADD COLUMN is_inventariable TINYINT(1) NOT NULL DEFAULT 0");

echo "<p class='ok'>✅ warehouse_products columns checked</p>";

// warehouse_stocks
$tables = array_column(array_map('get_object_vars', DB::select("SHOW TABLES LIKE 'warehouse_stocks'")), array_key_first(get_object_vars(DB::select("SHOW TABLES LIKE 'warehouse_stocks'")[0] ?? new stdClass)));
$stocksExists = DB::select("SHOW TABLES LIKE 'warehouse_stocks'");

if (empty($stocksExists)) {
    trySQL('CREATE warehouse_stocks', "
        CREATE TABLE warehouse_stocks (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            location_id BIGINT UNSIGNED NOT NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            quantity DECIMAL(10,2) NOT NULL DEFAULT 0,
            min_stock DECIMAL(10,2) NULL,
            optimal_stock DECIMAL(10,2) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY unique_location_product (location_id, product_id),
            FOREIGN KEY (location_id) REFERENCES warehouse_locations(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES warehouse_products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} else {
    echo "<p class='ok'>✅ warehouse_stocks already exists</p>";
}

// warehouse_movements
$movExists = DB::select("SHOW TABLES LIKE 'warehouse_movements'");
if (empty($movExists)) {
    trySQL('CREATE warehouse_movements', "
        CREATE TABLE warehouse_movements (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            movement_date DATETIME NOT NULL,
            user_id BIGINT UNSIGNED NULL,
            movement_type VARCHAR(50) NOT NULL,
            source_location_id BIGINT UNSIGNED NULL,
            destination_location_id BIGINT UNSIGNED NULL,
            product_id BIGINT UNSIGNED NOT NULL,
            quantity DECIMAL(10,2) NOT NULL,
            notes TEXT NULL,
            assigned_to_user_id BIGINT UNSIGNED NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (source_location_id) REFERENCES warehouse_locations(id) ON DELETE SET NULL,
            FOREIGN KEY (destination_location_id) REFERENCES warehouse_locations(id) ON DELETE SET NULL,
            FOREIGN KEY (product_id) REFERENCES warehouse_products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} else {
    echo "<p class='ok'>✅ warehouse_movements already exists</p>";
}

// mobile_devices spec columns
$mdTable = DB::select("SHOW TABLES LIKE 'mobile_devices'");
if (!empty($mdTable)) {
    $mdCols = array_column(array_map('get_object_vars', DB::select("SHOW COLUMNS FROM mobile_devices")), 'Field');
    $specCols = [
        'tipo'                 => "VARCHAR(50) NULL",
        'colore'               => "VARCHAR(100) NULL",
        'anno_acquisto'        => "INT NULL",
        'asset_code'           => "VARCHAR(100) NULL",
        'numero_telefono'      => "VARCHAR(50) NULL",
        'sistema_operativo'    => "VARCHAR(100) NULL",
        'versione_os'          => "VARCHAR(50) NULL",
        'dimensione_schermo'   => "DECIMAL(4,1) NULL",
        'memoria_ram'          => "VARCHAR(20) NULL",
        'memoria_storage'      => "VARCHAR(20) NULL",
        'processore'           => "VARCHAR(255) NULL",
        'fotocamera_principale'=> "VARCHAR(255) NULL",
        '5g'                   => "TINYINT(1) NOT NULL DEFAULT 0",
        'nfc'                  => "TINYINT(1) NOT NULL DEFAULT 0",
        'batteria_mah'         => "VARCHAR(50) NULL",
    ];
    $added = 0;
    foreach ($specCols as $col => $def) {
        if (!in_array($col, $mdCols)) {
            trySQL("mobile_devices.$col", "ALTER TABLE mobile_devices ADD COLUMN `$col` $def");
            $added++;
        }
    }
    echo "<p class='ok'>✅ mobile_devices: $added colonne aggiunte</p>";
}

// List warehouse tables
echo "<h3>Tabelle Warehouse nel DB:</h3><ul>";
$wTables = DB::select("SHOW TABLES LIKE 'warehouse%'");
foreach ($wTables as $t) { $v = array_values((array)$t)[0]; echo "<li>$v</li>"; }
echo "</ul>";

echo "<hr><h3>🔗 Links Rapidi:</h3><ul>
  <li><a href='/GestionaleCV/magazzino'>📦 Hub Magazzino</a></li>
  <li><a href='/GestionaleCV/magazzino/locations'>🗺️ Ubicazioni</a></li>
  <li><a href='/GestionaleCV/magazzino/prodotti'>📋 Prodotti</a></li>
  <li><a href='/GestionaleCV/magazzino/stock'>📊 Giacenze</a></li>
  <li><a href='/GestionaleCV/magazzino/movimenti'>🔁 Movimenti</a></li>
  <li><a href='/GestionaleCV/pc/aib/dispositivi-mobili'>📱 Dispositivi Mobili</a></li>
</ul>";
