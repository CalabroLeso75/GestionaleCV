<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Forcing table creation...\n";

try {
    if (!Schema::hasTable('vehicles')) {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('targa')->unique();
            $table->string('marca');
            $table->string('modello');
            $table->string('tipo'); 
            $table->date('immatricolazione_date')->nullable();
            $table->date('scadenza_assicurazione')->nullable();
            $table->date('scadenza_revisione')->nullable();
            $table->date('rottamazione_date')->nullable();
            $table->integer('km_attuali')->default(0);
            $table->string('stato')->default('disponibile');
            $table->timestamps();
        });
        echo "Table 'vehicles' created.\n";
    } else {
        echo "Table 'vehicles' already exists.\n";
    }

    if (!Schema::hasTable('vehicle_logs')) {
        Schema::create('vehicle_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('km_iniziali');
            $table->integer('km_finali')->nullable();
            $table->timestamp('assegnato_il');
            $table->timestamp('riconsegnato_il')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
        echo "Table 'vehicle_logs' created.\n";
    } else {
        echo "Table 'vehicle_logs' already exists.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
