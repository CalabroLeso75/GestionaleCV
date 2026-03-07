<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mobile_devices', function (Blueprint $table) {
            // Identificazione del dispositivo
            $table->string('tipo')->nullable()->default('smartphone')->after('modello')
                  ->comment('smartphone, tablet, altro');
            $table->string('colore')->nullable()->after('tipo');
            $table->year('anno_acquisto')->nullable()->after('colore');
            $table->string('numero_telefono')->nullable()->after('anno_acquisto')
                  ->comment('Se il dispositivo ha SIM integrata');

            // Specifiche tecniche (facoltative)
            $table->string('sistema_operativo')->nullable()->after('numero_telefono')
                  ->comment('iOS, Android, etc...');
            $table->string('versione_os')->nullable()->after('sistema_operativo');
            $table->decimal('dimensione_schermo', 4, 1)->nullable()->after('versione_os')
                  ->comment('In pollici, es. 6.1');
            $table->string('memoria_ram')->nullable()->after('dimensione_schermo')
                  ->comment('4GB, 8GB, 16GB...');
            $table->string('memoria_storage')->nullable()->after('memoria_ram')
                  ->comment('64GB, 128GB, 256GB...');
            $table->string('processore')->nullable()->after('memoria_storage');
            $table->string('fotocamera_principale')->nullable()->after('processore')
                  ->comment('Es. 12MP Dual, 48MP Triple...');
            $table->boolean('5g')->default(false)->after('fotocamera_principale');
            $table->boolean('nfc')->default(false)->after('5g');
            $table->string('batteria_mah')->nullable()->after('nfc');

            // Gestione aziendale
            $table->string('asset_code')->nullable()->unique()->after('batteria_mah')
                  ->comment('Codice inventariale aziendale interno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_devices', function (Blueprint $table) {
            $table->dropColumn([
                'tipo', 'colore', 'anno_acquisto', 'numero_telefono',
                'sistema_operativo', 'versione_os', 'dimensione_schermo',
                'memoria_ram', 'memoria_storage', 'processore',
                'fotocamera_principale', '5g', 'nfc', 'batteria_mah', 'asset_code',
            ]);
        });
    }
};
