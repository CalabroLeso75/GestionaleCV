<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('employees', 'internal_employees');
        
        Schema::table('internal_employees', function (Blueprint $table) {
            // Ensure FKs are handled if they existed, but we set them to NULL and didn't add constraints yet in previous script.
            // If we add constraints now, we should reference the new tables.
        });
    }

    public function down(): void
    {
        Schema::rename('internal_employees', 'employees');
    }
};
