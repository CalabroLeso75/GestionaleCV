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
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(0)->comment('Scorta minima');
            $table->decimal('optimal_stock', 10, 2)->default(0)->comment('Scorta ottimale');
            $table->timestamps();
            
            // Garantiamo un rigo di giacenza univoco per Prodotto-Ubicazione
            $table->unique(['warehouse_location_id', 'warehouse_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_stocks');
    }
};
