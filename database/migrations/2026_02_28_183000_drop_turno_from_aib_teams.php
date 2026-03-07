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
        Schema::table('aib_teams', function (Blueprint $table) {
            if (Schema::hasColumn('aib_teams', 'turno')) {
                $table->dropColumn('turno');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aib_teams', function (Blueprint $table) {
            $table->enum('turno', ['Mattina', 'Pomeriggio', 'Notte', 'H24'])->default('Mattina');
        });
    }
};
