<?php
try {
    if (!\Illuminate\Support\Facades\Schema::hasColumn('vehicles', 'ultima_revisione')) {
        \Illuminate\Support\Facades\Schema::table('vehicles', function ($table) {
            $table->date('ultima_revisione')->nullable()->after('immatricolazione_anno');
        });
        echo "FIX_SUCCESS: Column 'ultima_revisione' added.\n";
    } else {
        echo "FIX_SKIP: Column 'ultima_revisione' already exists.\n";
    }
} catch (\Exception $e) {
    echo "FIX_ERROR: " . $e->getMessage() . "\n";
}
