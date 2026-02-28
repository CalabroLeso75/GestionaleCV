<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('incidents')) {
            Schema::create('incidents', function (Blueprint $table) {
                $table->id();
                $table->string('codice_incidente')->unique(); // e.g., COR-2026-001
                $table->string('pcm_incidente_id')->nullable()->index(); // ID from PC2 system
                $table->dateTime('data_ora');
                $table->unsignedBigInteger('comune_id'); // Using unsignedBigInteger instead of constrained to avoid issues if cities table mapping is complex
                $table->string('indirizzo')->nullable();
                $table->decimal('lat', 10, 8)->nullable();
                $table->decimal('lon', 11, 8)->nullable();
                $table->string('tipo_evento'); // Incendio Boschivo, Allagamento, etc.
                $table->enum('priorita', ['Bassa', 'Media', 'Alta', 'Molto Alta', 'Critica'])->default('Media');
                $table->enum('stato', ['Aperto', 'In Gestione', 'Chiuso', 'Falso Allarme'])->default('Aperto');
                $table->text('descrizione')->nullable();
                $table->string('segnalatore')->nullable();
                $table->string('telefono_segnalatore')->nullable();
                $table->timestamps();
                
                // Foreign key removed temporarily to fix migration issues
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
