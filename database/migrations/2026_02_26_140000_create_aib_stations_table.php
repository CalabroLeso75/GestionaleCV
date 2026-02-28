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
        if (!Schema::hasTable('aib_stations')) {
            Schema::create('aib_stations', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->string('slug')->unique();
                $table->string('provincia'); // CS, CZ, KR, RC, VV
                $table->string('comune')->nullable();
                
                // Decimal Degrees (DD)
                $table->decimal('latitudine', 10, 8)->nullable();
                $table->decimal('longitudine', 11, 8)->nullable();
                
                // Degrees Minutes Seconds (DMS) string format or separate fields? 
                // Better as strings for display or separate for precision logic. 
                // In AIB usually: N 39° 12' 34.5"
                $table->string('lat_dms')->nullable();
                $table->string('lon_dms')->nullable();
                
                $table->enum('tipo', ['Base Operativa', 'Vedetta', 'Eliporto', 'Altro'])->default('Base Operativa');
                $table->enum('stato', ['Attivo', 'Non Attivo'])->default('Attivo');
                
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aib_stations');
    }
};
