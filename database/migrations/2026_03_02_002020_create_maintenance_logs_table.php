<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            
            $table->morphs('asset'); // Il bene in manutenzione (Vehicle, MobileDevice, ecc.)
            
            $table->string('tipo_evento'); // es: 'Preparazione Manutenzione', 'In Officina', 'Rientro da Manutenzione', 'Preparazione Demolizione', 'Demolito'
            $table->dateTime('data_evento');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->text('note_officina')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
