<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_devices', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modello');
            $table->string('imei')->nullable()->unique();
            $table->string('seriale')->nullable()->unique();
            $table->enum('stato', ['Attivo', 'Inattivo', 'Manutenzione', 'Dismesso'])->default('Attivo');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_devices');
    }
};
