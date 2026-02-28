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
            if (!Schema::hasColumn('aib_teams', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null')->after('station_id');
            }
            if (Schema::hasColumn('aib_teams', 'data')) {
                $table->renameColumn('data', 'data_inizio');
            }
        });

        Schema::table('aib_teams', function (Blueprint $table) {
            if (!Schema::hasColumn('aib_teams', 'data_fine')) {
                $table->date('data_fine')->nullable()->after('data_inizio');
            }
            if (!Schema::hasColumn('aib_teams', 'campagna')) {
                $table->string('campagna')->nullable()->after('data_fine');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aib_teams', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn(['vehicle_id', 'data_fine', 'campagna']);
            $table->renameColumn('data_inizio', 'data');
        });
    }
};
