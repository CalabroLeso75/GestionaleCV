<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeesImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = base_path('../da_eliminare/anag_persone.sql');

        if (!file_exists($sqlPath)) {
            $this->command->error("File not found: $sqlPath");
            return;
        }

        // 1. Drop temp table if exists
        Schema::dropIfExists('anag_persone');

        // 2. Load the legacy SQL file
        // We need to split the file or clean it because DB::unprepared doesn't handle delimiter changes inside dumps well usually, 
        // but for a simple mysqldump it usually works. 
        // We might need to filter out lines that cause issues if any.
        // Given the file content preview, it looks standard.
        
        $sql = file_get_contents($sqlPath);
        
        // Basic cleanup if needed (e.g. avoiding SET SQL_MODE issues if strict)
        // For now, try running it directly.
        try {
            DB::unprepared($sql);
            $this->command->info("Legacy table 'anag_persone' loaded successfully.");
        } catch (\Exception $e) {
            $this->command->error("Error loading SQL: " . $e->getMessage());
            return;
        }

        // 3. Map and Insert Data
        // Mapping:
        // nome -> first_name
        // cognome -> last_name
        // codice_fiscale -> tax_code
        // matricola -> badge_number
        // data_nascita -> birth_date
        // luogo_nascita_testo -> birth_place
        // genere -> gender (uomo->male, donna->female)
        // ccnl_posizione -> position
        // tipo_personale -> employee_type (interno->internal, esterno->external)
        // stato_rapporto -> status (operativo->active, cessato->terminated, sospeso->suspended, in_attesa->pending)
        // email_aziendale -> email
        // email_personale -> personal_email
        // telefono_aziendale -> phone
        // telefono_personale -> personal_phone
        // requisiti_aib -> is_aib_qualified
        // disponibile_emergenze -> is_emergency_available
        // ruoli_operativi -> operational_roles (already JSON)
        // note -> notes

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

        $count = DB::table('employees')->count();
        $this->command->info("Imported $count employees successfully.");

        // 4. Cleanup
        Schema::dropIfExists('anag_persone');
    }
}
