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
        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'localita')) {
                $table->string('localita')->nullable()->after('citta');
            }
        });

        Schema::table('aib_stations', function (Blueprint $table) {
            if (!Schema::hasColumn('aib_stations', 'localita')) {
                $table->string('localita')->nullable()->after('comune');
            }
            if (!Schema::hasColumn('aib_stations', 'location_id')) {
                $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('localita');
        });

        Schema::table('aib_stations', function (Blueprint $table) {
            $table->dropColumn('localita');
        });
    }
};
