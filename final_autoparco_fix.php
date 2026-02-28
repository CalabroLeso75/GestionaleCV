<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Fixing PC areas...\n";

// Ensure system_areas has autoparco
$area = DB::table('system_areas')->where('slug', 'autoparco')->first();
if (!$area) {
    echo "Creating autoparco area...\n";
    DB::table('system_areas')->insert([
        'name' => 'Autoparco',
        'slug' => 'autoparco',
        'description' => 'Gestione Parco Macchine e Mezzi Operativi',
        'is_active' => true,
        'sort_order' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
} else {
    echo "Updating existing autoparco area...\n";
    DB::table('system_areas')->where('slug', 'autoparco')->update(['is_active' => true]);
}

// Ensure user 1 has permission
$userArea = DB::table('user_area_roles')->where('user_id', 1)->where('area', 'autoparco')->first();
if (!$userArea) {
    echo "Assigning role for user 1...\n";
    DB::table('user_area_roles')->insert([
        'user_id' => 1,
        'area' => 'autoparco',
        'role' => 'Responsabile Autoparco',
        'privilege_level' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Fix missing tables
if (!Schema::hasTable('vehicles')) {
    echo "Creating vehicles table...\n";
    Schema::create('vehicles', function ($table) {
        $table->id();
        $table->string('targa')->unique();
        $table->string('marca');
        $table->string('modello');
        $table->string('tipo');
        $table->date('immatricolazione_date')->nullable();
        $table->date('scadenza_assicurazione')->nullable();
        $table->date('scadenza_revisione')->nullable();
        $table->date('rottamazione_date')->nullable();
        $table->integer('km_attuali')->default(0);
        $table->string('stato')->default('disponibile');
        $table->timestamps();
    });
}

if (!Schema::hasTable('vehicle_logs')) {
    echo "Creating vehicle_logs table...\n";
    Schema::create('vehicle_logs', function ($table) {
        $table->id();
        $table->foreignId('vehicle_id')->constrained('vehicles');
        $table->foreignId('user_id')->constrained('users');
        $table->integer('km_iniziali');
        $table->integer('km_finali')->nullable();
        $table->timestamp('assegnato_il')->nullable();
        $table->timestamp('riconsegnato_il')->nullable();
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

echo "Done fixed everything.\n";
