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
        Schema::create('warehouse_products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable()->comment('Codice interno');
            $table->string('barcode')->unique()->index()->nullable()->comment('EAN/UPC/GTIN o altro barcode scannerizzabile');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->string('unit_of_measure')->default('pz')->comment('pz, kg, litri, mt, etc');
            $table->boolean('is_inventariable')->default(false)->comment('Se vero, necessita di matricola/seriale per tracciamento dettagliato');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_products');
    }
};
