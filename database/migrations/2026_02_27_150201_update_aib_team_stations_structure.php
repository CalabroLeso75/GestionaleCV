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
            $table->dropForeign(['station_id']);
            $table->dropColumn('station_id');
        });

        Schema::create('aib_team_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('aib_teams')->onDelete('cascade');
            $table->foreignId('station_id')->constrained('aib_stations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aib_team_stations');
        
        Schema::table('aib_teams', function (Blueprint $table) {
            $table->foreignId('station_id')->nullable()->constrained('aib_stations')->onDelete('cascade');
        });
    }
};
