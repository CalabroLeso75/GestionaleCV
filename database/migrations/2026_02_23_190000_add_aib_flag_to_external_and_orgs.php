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
        if (!Schema::hasColumn('external_employees', 'is_aib')) {
            Schema::table('external_employees', function (Blueprint $table) {
                $table->boolean('is_aib')->default(false)->after('job_title');
            });
        }

        if (!Schema::hasColumn('organizations', 'is_aib')) {
            Schema::table('organizations', function (Blueprint $table) {
                $table->boolean('is_aib')->default(false)->after('vat_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_employees', function (Blueprint $table) {
            $table->dropColumn('is_aib');
        });

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('is_aib');
        });
    }
};
