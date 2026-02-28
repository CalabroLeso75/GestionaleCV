<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('vehicles', 'ultima_revisione')) {
    Schema::table('vehicles', function (Blueprint $table) {
        $table->date('ultima_revisione')->nullable()->after('immatricolazione_anno');
    });
    echo "Column 'ultima_revisione' added successfully.\n";
} else {
    echo "Column 'ultima_revisione' already exists.\n";
}
