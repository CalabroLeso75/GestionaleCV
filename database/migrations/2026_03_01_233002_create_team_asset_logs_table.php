<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_asset_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('aib_teams')->onDelete('cascade');
            $table->string('asset_type'); // es. Veicolo, Telefono, Dispositivo Mobile
            $table->string('asset_name'); // es. Targa, Numero Tel, o IMEI
            $table->enum('action', ['Consegna Iniziale', 'Cambio Assegnatari', 'Ritiro']);
            $table->json('old_assignees')->nullable(); // Array di nomi capisquadra prima (null se iniziale)
            $table->json('new_assignees')->nullable(); // Array di nomi capisquadra dopo
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Chi ha fatto l'operazione
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_asset_logs');
    }
};
