<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dashboard_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('dashboard_sections', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('dashboard_sections')->onDelete('cascade');
            }
            if (!Schema::hasColumn('dashboard_sections', 'level')) {
                $table->unsignedTinyInteger('level')->default(1)->after('parent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dashboard_sections', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'level']);
        });
    }
};
