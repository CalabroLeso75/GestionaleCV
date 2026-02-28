<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "UPDATING SCHEMA...\n";

try {
    if (Schema::hasTable('dashboard_sections') && !Schema::hasColumn('dashboard_sections', 'required_area')) {
        Schema::table('dashboard_sections', function (Blueprint $table) {
            $table->string('required_area')->nullable()->after('required_role');
        });
        echo "✅ Column 'required_area' added successfully.\n";
    } else {
        echo "ℹ️ Column 'required_area' already exists or table missing.\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
