<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aib_team_mobile_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('aib_teams')->onDelete('cascade');
            $table->foreignId('mobile_device_id')->constrained('mobile_devices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aib_team_mobile_devices');
    }
};
