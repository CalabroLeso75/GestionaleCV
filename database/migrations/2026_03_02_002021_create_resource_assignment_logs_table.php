<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::dropIfExists('resource_assignment_logs');
        Schema::create('resource_assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_assignment_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('assignable_type');
            $table->unsignedBigInteger('assignable_id');
            
            $table->string('assignee_type')->nullable();
            $table->unsignedBigInteger('assignee_id')->nullable();
            
            $table->string('azione'); // 'Assegnazione', 'Restituzione', 'Cambio Assegnatario'
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Chi ha compiuto l'azione a sistema
            
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_assignment_logs');
    }
};
