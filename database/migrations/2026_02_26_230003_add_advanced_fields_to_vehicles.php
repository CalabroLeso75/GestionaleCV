<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'vehicle_type_id')) {
                $table->foreignId('vehicle_type_id')->nullable()->constrained('vehicle_types');
            }
            if (!Schema::hasColumn('vehicles', 'scadenza_verifica_sicurezza')) {
                $table->date('scadenza_verifica_sicurezza')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['vehicle_type_id']);
            $table->dropColumn(['vehicle_type_id', 'scadenza_verifica_sicurezza']);
        });
    }
};
