<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeesMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if source table exists and has data
        if (!Schema::hasTable('anag_persone')) {
            $this->command->error("Table 'anag_persone' not found. Please import the SQL dump first.");
            return;
        }

        $count = DB::table('anag_persone')->count();
        $this->command->info("Found $count records in 'anag_persone'. processing mapping...");

        // Map and Insert Data
        // Mapping matches previous logic
        DB::statement("
            INSERT INTO employees (
                first_name, last_name, tax_code, badge_number, 
                birth_date, birth_place, gender, 
                position, employee_type, status, 
                email, personal_email, phone, personal_phone, 
                is_aib_qualified, is_emergency_available, operational_roles, notes, created_at, updated_at
            )
            SELECT 
                nome, 
                cognome, 
                codice_fiscale, 
                matricola, 
                data_nascita, 
                luogo_nascita_testo, 
                CASE 
                    WHEN genere = 'uomo' THEN 'male' 
                    WHEN genere = 'donna' THEN 'female' 
                    ELSE NULL 
                END,
                ccnl_posizione, 
                CASE 
                    WHEN tipo_personale = 'interno' THEN 'internal' 
                    WHEN tipo_personale = 'esterno' THEN 'external' 
                    ELSE 'internal' 
                END,
                CASE 
                    WHEN stato_rapporto = 'operativo' THEN 'active' 
                    WHEN stato_rapporto = 'cessato' THEN 'terminated' 
                    WHEN stato_rapporto = 'sospeso' THEN 'suspended' 
                    WHEN stato_rapporto = 'in_attesa' THEN 'pending' 
                    ELSE 'active' 
                END,
                email_aziendale, 
                email_personale, 
                telefono_aziendale, 
                telefono_personale, 
                requisiti_aib, 
                disponibile_emergenze, 
                ruoli_operativi, 
                note, 
                NOW(), 
                NOW()
            FROM anag_persone
        ");

        $newCount = DB::table('employees')->count();
        $this->command->info("Mapped $newCount employees successfully.");

        // Cleanup
        Schema::dropIfExists('anag_persone');
    }
}
