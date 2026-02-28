<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            
            $table->string('first_name');
            $table->string('last_name');
            $table->string('tax_code')->unique();
            
            $table->date('birth_date');
            $table->foreignId('birth_city_id')->nullable()->constrained('localizz_comune')->nullOnDelete();
            $table->string('birth_place_text')->nullable(); // For foreign places or if city not found
            
            $table->string('job_title')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_employees');
    }
};
