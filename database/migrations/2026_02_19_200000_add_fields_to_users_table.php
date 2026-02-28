<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->after('name')->nullable();
            $table->string('fiscal_code', 16)->unique()->nullable()->after('surname');
            $table->date('birth_date')->nullable()->after('fiscal_code');
            $table->unsignedBigInteger('birth_city_id')->nullable()->after('birth_date');
            $table->unsignedBigInteger('birth_country_id')->nullable()->after('birth_city_id');
            // 'internal' = dipendente, 'external' = esterno
            $table->enum('type', ['internal', 'external', 'unknown'])->default('unknown')->after('birth_country_id');
            // 'pending' = in attesa di approvazione, 'active' = approvato, 'suspended' = sospeso
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending')->after('type');
            $table->string('otp_code', 8)->nullable()->after('password');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');

            // Foreign keys (Best verify table names first, usually 'localizz_comune' and 'localizz_statoestero' based on previous context)
            // I will add them in a separate step if needed to avoid migration failure on constraints.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'surname',
                'fiscal_code',
                'birth_date',
                'birth_city_id',
                'birth_country_id',
                'type',
                'status',
                'otp_code',
                'otp_expires_at'
            ]);
        });
    }
};
