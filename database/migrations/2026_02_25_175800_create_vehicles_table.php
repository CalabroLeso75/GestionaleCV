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
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('targa')->unique();
                $table->string('marca');
                $table->string('modello');
                $table->string('tipo'); // Auto, Pickup, Autobotte, etc.
                $table->date('immatricolazione_date')->nullable();
                $table->date('scadenza_assicurazione')->nullable();
                $table->date('scadenza_revisione')->nullable();
                $table->date('rottamazione_date')->nullable();
                $table->integer('km_attuali')->default(0);
                $table->enum('stato', ['disponibile', 'in uso', 'manutenzione', 'fuori servizio'])->default('disponibile');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
