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
        if (!Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->string('nome'); // Es. "Distretto 1", "Sede Centrale"
                // enum: Sede Centrale, Distretto, Officina, Parco Macchine, Vivaio, Sala Operativa, Magazzino, Distaccamento
                $table->string('tipo_sede')->default('Distretto'); 
                $table->string('indirizzo')->nullable();
                $table->string('citta')->nullable();
                $table->string('provincia', 2)->nullable();
                $table->string('cap', 5)->nullable();
                $table->decimal('lat', 10, 8)->nullable();
                $table->decimal('lng', 11, 8)->nullable();
                $table->text('note_operative')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
