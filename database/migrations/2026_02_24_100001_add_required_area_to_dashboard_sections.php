<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('dashboard_sections') && !Schema::hasColumn('dashboard_sections', 'required_area')) {
            Schema::table('dashboard_sections', function (Blueprint $table) {
                $table->string('required_area')->nullable()->after('required_role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dashboard_sections', 'required_area')) {
            Schema::table('dashboard_sections', function (Blueprint $table) {
                $table->dropColumn('required_area');
            });
        }
    }
};
