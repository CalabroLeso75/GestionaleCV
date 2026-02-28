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
        Schema::table('company_phones', function (Blueprint $table) {
            $table->string('imei')->nullable()->after('alias');
            $table->string('operatore')->nullable()->after('imei');
            $table->string('piano_telefonico')->nullable()->after('operatore');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_phones', function (Blueprint $table) {
            $table->dropColumn(['imei', 'operatore', 'piano_telefonico']);
        });
    }
};
