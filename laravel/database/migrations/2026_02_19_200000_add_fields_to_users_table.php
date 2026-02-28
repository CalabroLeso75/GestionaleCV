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
            $table->string('fiscal_code', 16)->unique()->after('surname')->nullable();
            $table->date('birth_date')->after('fiscal_code')->nullable();
            $table->unsignedBigInteger('birth_city_id')->nullable()->after('birth_date');
            $table->unsignedBigInteger('birth_country_id')->nullable()->after('birth_city_id');
            $table->enum('type', ['internal', 'external', 'unknown'])->default('unknown')->after('birth_country_id');
            $table->enum('status', ['pending', 'active', 'suspended'])->default('pending')->after('type');
            $table->string('otp_code', 8)->nullable()->after('password');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');

            // Foreign keys (assuming cities and foreign_states tables exist)
            // We use standard names based on previous conversions: 'cities' (localizz_comune?) NO, I need to check the table names created earlier.
            // checking implementation plan... it says `localizz_comune` and `localizz_statoestero` are the tables?
            // Wait, previous conversation said "Created Location/City model".
            // Let's check the actual table names used in the models or database.
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
