<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Toponym;

class ImportToponyms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:toponyms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import toponyms from the legacy CSV file into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $csvPath = base_path('da eliminare/data/toponimi_calabria.csv');
        
        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return Command::FAILURE;
        }

        $this->info("Importing toponyms from CSV...");

        if (($handle = fopen($csvPath, "r")) !== false) {
            // Skip header
            fgetcsv($handle);
            
            $count = 0;
            $batch = [];
            
            while (($data = fgetcsv($handle)) !== false) {
                if (count($data) >= 3) {
                    $batch[] = [
                        'name' => $data[0],
                        'latitude' => (float) $data[1],
                        'longitude' => (float) $data[2],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $count++;
                    
                    if (count($batch) >= 500) {
                        Toponym::insert($batch);
                        $batch = [];
                    }
                }
            }
            
            if (count($batch) > 0) {
                Toponym::insert($batch);
            }
            
            fclose($handle);
            
            $this->info("Successfully imported {$count} toponyms!");
            return Command::SUCCESS;
        }
        
        $this->error("Could not open CSV file.");
        return Command::FAILURE;
    }
}
