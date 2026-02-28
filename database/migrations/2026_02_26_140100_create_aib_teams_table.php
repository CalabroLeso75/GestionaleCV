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
        // 1. AIB Teams (Squadre)
        if (!Schema::hasTable('aib_teams')) {
            Schema::create('aib_teams', function (Blueprint $table) {
                $table->id();
                $table->string('sigla')->unique(); // XXREY001
                $table->foreignId('station_id')->constrained('aib_stations')->onDelete('cascade');
                $table->date('data');
                $table->enum('turno', ['Mattina', 'Pomeriggio', 'Notte', 'H24'])->default('Mattina');
                $table->enum('stato_operativo', ['Pronto', 'In Intervento', 'Fuori Servizio'])->default('Pronto');
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        // 2. Team Members (Pivot with Roles)
        if (!Schema::hasTable('aib_team_members')) {
            Schema::create('aib_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained('aib_teams')->onDelete('cascade');
                
                // Member can be Internal or External (using morphs or generic ID)
                // For now, let's assume we link to a generic 'person' interface or just store ID and Type
                $table->unsignedBigInteger('member_id');
                $table->string('member_type'); // App\Models\InternalEmployee or App\Models\ExternalEmployee
                
                $table->enum('ruolo', ['RES', 'REB', 'REV', 'REP']); // Responsabile, Barelliere, Vedetta, Pilota
                $table->timestamps();
                
                $table->index(['member_id', 'member_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aib_team_members');
        Schema::dropIfExists('aib_teams');
    }
};
