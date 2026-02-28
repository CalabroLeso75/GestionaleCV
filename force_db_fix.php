<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Checking vehicles table...\n";
if (!Schema::hasTable('vehicles')) {
    echo "Creating vehicles table...\n";
    Schema::create('vehicles', function (Blueprint $table) {
        $table->id();
        $table->string('targa')->unique();
        $table->string('marca');
        $table->string('modello');
        $table->string('tipo'); // es. pick-up, autobotte
        $table->integer('km_attuali')->default(0);
        $table->date('scadenza_assicurazione')->nullable();
        $table->date('scadenza_revisione')->nullable();
        $table->string('stato')->default('disponibile'); // disponibile, in uso, fuori servizio
        $table->timestamps();
    });
    echo "Done.\n";
} else {
    echo "Table already exists.\n";
}

if (!Schema::hasTable('vehicle_logs')) {
    echo "Creating vehicle_logs table...\n";
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
    echo "Done.\n";
}
