<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RobustImportSeeder extends Seeder
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

        // Clean up
        Schema::dropIfExists('anag_persone');

        $this->command->info("Reading SQL file...");
        $handle = fopen($sqlPath, "r");
        
        if (!$handle) {
            $this->command->error("Could not open file.");
            return;
        }

        $buffer = "";
        $count = 0;

        while (($line = fgets($handle)) !== false) {
            $trimLine = trim($line);
            
            // Skip comments and empty lines
            if (empty($trimLine) || str_starts_with($trimLine, '--') || str_starts_with($trimLine, '/*')) {
                continue;
            }

            $buffer .= $line;

            // Simple check for statement end (semicolon at end of line)
            // This assumes standard mysqldump formatting where ; is at the end of the statement line.
            if (str_ends_with($trimLine, ';')) {
                try {
                    DB::statement($buffer);
                    $count++;
                    if ($count % 100 == 0) {
                        $this->command->info("Executed $count statements...");
                    }
                } catch (\Exception $e) {
                    $this->command->warn("Error executing statement: " . substr($buffer, 0, 50) . "...");
                    // $this->command->warn($e->getMessage());
                }
                $buffer = "";
            }
        }

        fclose($handle);
        $this->command->info("Import complete. Total statements executed: $count");
        
        $tableCount = DB::table('anag_persone')->count();
        $this->command->info("Records in 'anag_persone': $tableCount");
    }
}
