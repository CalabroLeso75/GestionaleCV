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
        Schema::create('emergency_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role_snapshot'); // dos o operatore
            $table->decimal('op_lat', 10, 7)->nullable();
            $table->decimal('op_lng', 10, 7)->nullable();
            $table->decimal('fire_lat', 10, 7);
            $table->decimal('fire_lng', 10, 7);
            $table->decimal('distance', 10, 2)->nullable(); // metri

            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('toponym')->nullable();

            $table->decimal('temperature', 5, 2)->nullable();
            $table->decimal('wind_speed', 5, 2)->nullable();
            $table->string('wind_direction')->nullable();

            // Forecasts
            $table->decimal('wind_forecast_2h_speed', 5, 2)->nullable();
            $table->string('wind_forecast_2h_dir')->nullable();
            $table->decimal('wind_forecast_2h_gust', 5, 2)->nullable();

            $table->decimal('wind_forecast_4h_speed', 5, 2)->nullable();
            $table->string('wind_forecast_4h_dir')->nullable();
            $table->decimal('wind_forecast_4h_gust', 5, 2)->nullable();

            $table->decimal('wind_forecast_6h_speed', 5, 2)->nullable();
            $table->string('wind_forecast_6h_dir')->nullable();
            $table->decimal('wind_forecast_6h_gust', 5, 2)->nullable();

            $table->text('notes')->nullable();

            // GIS
            $table->longText('polygon_geojson')->nullable();
            $table->decimal('area_hectares', 10, 2)->nullable();
            $table->decimal('front_meters', 10, 2)->nullable();
            $table->string('kml_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_reports');
    }
};
