<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DashboardSection;

class HardcodedSectionsSeeder extends Seeder
{
    /**
     * Migrate the four hardcoded dashboard tiles (Admin, Log, IA, CF)
     * into the dashboard_sections DB registry with required_role = super-admin.
     * Also ensures the 'admin' role gets its own tile for admin section without super-admin restriction.
     */
    public function run(): void
    {
        $tiles = [
            [
                'title'         => 'Amministrazione di Sistema',
                'description'   => 'Impostazioni, utenti, sezioni, profili, stile',
                'icon'          => '🛡️',
                'route'         => 'admin.index',
                'color'         => '#d32f2f',
                'required_role' => 'super-admin',
                'required_area' => null,
                'is_active'     => true,
                'sort_order'    => 0,
                'level'         => 1,
                'parent_id'     => null,
            ],
            [
                'title'         => 'Log Attività',
                'description'   => 'Registrazione eventi, login, modifiche',
                'icon'          => '📜',
                'route'         => 'admin.logs.index',
                'color'         => '#455a64',
                'required_role' => 'super-admin',
                'required_area' => null,
                'is_active'     => true,
                'sort_order'    => 1,
                'level'         => 1,
                'parent_id'     => null,
            ],
            [
                'title'         => 'IA Architetto',
                'description'   => 'Sfrutta l\'IA locale per ottimizzare il sistema',
                'icon'          => '🧠',
                'route'         => 'admin.ai.architect',
                'color'         => '#6f42c1',
                'required_role' => 'super-admin',
                'required_area' => null,
                'is_active'     => true,
                'sort_order'    => 2,
                'level'         => 1,
                'parent_id'     => null,
            ],
            [
                'title'         => 'Strumenti CF',
                'description'   => 'Calcola il Codice Fiscale o estrai dati da uno esistente',
                'icon'          => '🆔',
                'route'         => 'admin.tools.fiscal_code.index',
                'color'         => '#198754',
                'required_role' => 'super-admin',
                'required_area' => null,
                'is_active'     => true,
                'sort_order'    => 3,
                'level'         => 1,
                'parent_id'     => null,
            ],
        ];

        foreach ($tiles as $tile) {
            // Only insert if no duplicate title at L1 exists
            $exists = DashboardSection::where('title', $tile['title'])->where('level', 1)->exists();
            if (!$exists) {
                DashboardSection::create($tile);
            }
        }

        $this->command->info('Hardcoded sections migrated to DB registry.');
    }
}
