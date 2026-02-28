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
        Schema::table('aib_teams', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
            
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE aib_teams MODIFY COLUMN stato_operativo ENUM('Pronto', 'Incompleta', 'In Intervento', 'Fuori Servizio') DEFAULT 'Pronto'");
        });

        Schema::create('aib_team_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('aib_teams')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('aib_team_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('aib_teams')->onDelete('cascade');
            $table->foreignId('phone_id')->constrained('company_phones')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('aib_team_members', function (Blueprint $table) {
            $table->dropColumn('ruolo');
            $table->boolean('is_caposquadra')->default(false)->after('member_type');
            $table->boolean('is_autista')->default(false)->after('is_caposquadra');
            $table->string('ruolo_specifico')->nullable()->after('is_autista');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aib_team_members', function (Blueprint $table) {
            $table->dropColumn(['is_caposquadra', 'is_autista', 'ruolo_specifico']);
            $table->enum('ruolo', ['RES', 'REB', 'REV', 'REP'])->default('RES');
        });

        Schema::dropIfExists('aib_team_phones');
        Schema::dropIfExists('aib_team_vehicles');

        Schema::table('aib_teams', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE aib_teams MODIFY COLUMN stato_operativo ENUM('Pronto', 'In Intervento', 'Fuori Servizio') DEFAULT 'Pronto'");
        });
    }
};
