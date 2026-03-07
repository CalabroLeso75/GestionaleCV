<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('role_sections')) {
            Schema::create('role_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('dashboard_section_id');
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('dashboard_section_id')->references('id')->on('dashboard_sections')->onDelete('cascade');
                $table->unique(['role_id', 'dashboard_section_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_sections');
    }
};
