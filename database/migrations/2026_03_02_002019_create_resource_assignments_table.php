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
        Schema::create('resource_assignments', function (Blueprint $table) {
            $table->id();
            
            // L'oggetto assegnato (Veicolo, Telefono, Dispositivo Mobile, ecc.)
            $table->morphs('assignable');
            
            // Il soggetto a cui è assegnato (Autista, Operatore, Dipendente Esterno, ecc.)
            $table->morphs('assignee');
            
            $table->dateTime('data_assegnazione');
            $table->dateTime('data_restituzione')->nullable();
            
            $table->text('note_assegnazione')->nullable();
            $table->text('note_restituzione')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_assignments');
    }
};
