<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['public', 'private'])->default('private');
            $table->string('tax_code')->nullable(); // Codice Fiscale
            $table->string('vat_number')->nullable(); // Partita IVA
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Address / Location
            $table->foreignId('city_id')->nullable()->constrained('localizz_comune')->nullOnDelete();
            $table->string('address')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
