<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use App\Models\DashboardSection;
use Illuminate\Support\Facades\DB;

// 1. Create the DOS role
$roleDOS = Role::findOrCreate('dos');
echo "Ruolo 'dos' " . ($roleDOS->wasRecentlyCreated ? "creato" : "esistente") . ".\n";

$pcSection = DashboardSection::where('title', 'like', '%Protezione Civile%')->first();
$aibSection = DashboardSection::where('title', 'like', '%A.I.B.%')->orWhere('route', 'like', '%pc/aib%')->first();
$dosSection = DashboardSection::where('title', 'like', '%Strumenti D.O.S.%')->orWhere('route', 'like', '%dos%')->first();

$sectionIds = collect([$pcSection, $aibSection, $dosSection])->filter()->pluck('id')->toArray();

if (empty($sectionIds)) {
    echo "Attenzione: nessuna sezione trovata per l'assegnazione.\n";
} else {
    foreach ($sectionIds as $sid) {
        DB::table('role_sections')->updateOrInsert([
            'role_id' => $roleDOS->id,
            'dashboard_section_id' => $sid
        ]);
    }
    echo "Sezioni " . implode(', ', $sectionIds) . " assegnate al ruolo 'dos'.\n";
}
