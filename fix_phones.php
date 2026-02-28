<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    if (!Schema::hasColumn('company_phones', 'imei')) {
        Schema::table('company_phones', function (Blueprint $table) {
            $table->string('imei')->nullable()->after('alias');
            $table->string('operatore')->nullable()->after('imei');
            $table->string('piano_telefonico')->nullable()->after('operatore');
        });
        echo "Colonne aggiunte con successo.\n";
    } else {
        echo "Colonne gia presenti.\n";
    }

    // Also let's fix the migrations table so it doesn't try to create company_phones again
    if (!\Illuminate\Support\Facades\DB::table('migrations')->where('migration', '2026_02_27_122401_create_company_phones_table')->exists()) {
        \Illuminate\Support\Facades\DB::table('migrations')->insert([
            'migration' => '2026_02_27_122401_create_company_phones_table',
            'batch' => 2 // just put a batch number
        ]);
        echo "Migration record inserito per create_company_phones_table.\n";
    }
    
    if (!\Illuminate\Support\Facades\DB::table('migrations')->where('migration', '2026_02_27_153000_add_details_to_company_phones_table')->exists()) {
        \Illuminate\Support\Facades\DB::table('migrations')->insert([
            'migration' => '2026_02_27_153000_add_details_to_company_phones_table',
            'batch' => 2
        ]);
        echo "Migration record inserito per add_details_to_company_phones_table.\n";
    }

} catch (\Exception $e) {
    echo "Errore: " . $e->getMessage() . "\n";
}
