<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('vehicle_types')) {
            Schema::create('vehicle_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('documentazione')->nullable();
                $table->text('certificazioni')->nullable();
                $table->string('patente')->nullable();
                $table->string('revisione')->nullable();
                $table->string('assicurazione')->nullable();
                $table->string('tipo_abilitazione')->nullable();
                $table->string('ente_controllo')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('vehicle_types');
    }
};
