<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "--- Altering Tables for Privilege System ---\n";

// 1. Add parent_id to system_areas
if (!Schema::hasColumn('system_areas', 'parent_id')) {
    Schema::table('system_areas', function (Blueprint $table) {
        $table->unsignedBigInteger('parent_id')->nullable()->after('id');
        $table->foreign('parent_id')->references('id')->on('system_areas')->onDelete('cascade');
    });
    echo "✓ Added parent_id to system_areas\n";
} else {
    echo "! parent_id already exists in system_areas\n";
}

// 2. Add privilege_level to user_area_roles
if (!Schema::hasColumn('user_area_roles', 'privilege_level')) {
    Schema::table('user_area_roles', function (Blueprint $table) {
        $table->integer('privilege_level')->default(1)->after('area');
    });
    echo "✓ Added privilege_level to user_area_roles\n";
} else {
    echo "! privilege_level already exists in user_area_roles\n";
}

echo "\nDatabase migration completed!\n";
