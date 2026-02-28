<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::rename('employees', 'internal_employees');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('internal_employees')) {
            Schema::rename('internal_employees', 'employees');
        }
    }
};
