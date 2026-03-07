<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter table using raw SQL for Enum compatibility or converting to varchar
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN stato VARCHAR(50) DEFAULT 'operativo'");
        
        // Update any old 'disponibile' to 'operativo'
        DB::statement("UPDATE vehicles SET stato = 'operativo' WHERE stato = 'disponibile'");
        DB::statement("UPDATE vehicles SET stato = 'operativo' WHERE stato = '' OR stato IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN stato ENUM('disponibile', 'in uso', 'manutenzione', 'fuori servizio') DEFAULT 'disponibile'");
    }
};
