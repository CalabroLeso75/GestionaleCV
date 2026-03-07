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
        Schema::create('warehouse_movements', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_time');
            $table->foreignId('user_id')->constrained()->comment('Utente che ha registrato il movimento');
            $table->string('type')->comment('CARICO, TRASFERIMENTO, SMISTAMENTO, SCARICO, ASSEGNAZIONE, RITORNO, INVENTARIO');
            
            $table->foreignId('source_location_id')->nullable()->constrained('warehouse_locations');
            $table->foreignId('destination_location_id')->nullable()->constrained('warehouse_locations');
            
            $table->foreignId('warehouse_product_id')->constrained()->cascadeOnDelete();
            
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            
            // Assegnazioni specifiche
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->comment('Se assegnato a persona');
            // se servono altri campi polimorfici per uffici/mezzi, li si può aggiungere in seguito
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_movements');
    }
};
