<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/debug-state', function () {
    $out = "<h1>State Debug</h1>";
    
    // DB
    try {
        $out .= "<h2>Database</h2>";
        $out .= "Vehicles: " . (\Illuminate\Support\Facades\Schema::hasTable('vehicles') ? \App\Models\Vehicle::count() : "MISSING") . "<br>";
        $out .= "Dashboard Sections: " . (\Illuminate\Support\Facades\Schema::hasTable('dashboard_sections') ? \App\Models\DashboardSection::count() : "MISSING") . "<br>";
        
        if (\Illuminate\Support\Facades\Schema::hasTable('dashboard_sections')) {
            $sections = \App\Models\DashboardSection::all();
            $out .= "<ul>";
            foreach($sections as $s) {
                $out .= "<li>{$s->title} (Route: {$s->route}, Active: {$s->is_active}, Role: {$s->required_role}, Area: {$s->required_area})</li>";
            }
            $out .= "</ul>";
        }
        
    } catch (\Exception $e) {
        $out .= "Error DB: " . $e->getMessage() . "<br>";
    }

    // Files
    try {
        $out .= "<h2>Files</h2>";
        $files = [
            'public/svg/sprites.svg',
            'svg/sprites.svg',
            'bootstrap-italia/dist/svg/sprites.svg',
            'public/bootstrap-italia/dist/svg/sprites.svg',
        ];
        foreach ($files as $f) {
            $path = base_path($f);
            $out .= "Checking $f: " . (file_exists($path) ? "EXISTS" : "MISSING") . "<br>";
        }
        
        $out .= "Base path: " . base_path() . "<br>";
        $out .= "Public path: " . public_path() . "<br>";
    } catch (\Exception $e) {
        $out .= "Error FS: " . $e->getMessage() . "<br>";
    }

    return $out;
});

Route::get('/force-fix-db', function () {
    $results = [];
    try {
        // Ensure tables
        if (!\Illuminate\Support\Facades\Schema::hasTable('vehicles')) {
            \Illuminate\Support\Facades\Schema::create('vehicles', function ($table) {
                $table->id();
                $table->string('targa')->unique();
                $table->string('marca');
                $table->string('modello');
                $table->string('tipo');
                $table->date('immatricolazione_date')->nullable();
                $table->integer('immatricolazione_mese')->nullable();
                $table->integer('immatricolazione_anno')->nullable();
                $table->string('assicurazione_compagnia')->nullable();
                $table->string('assicurazione_polizza')->nullable();
                $table->date('scadenza_assicurazione')->nullable();
                $table->date('assicurazione_copertura')->nullable();
                $table->date('scadenza_revisione')->nullable();
                $table->date('rottamazione_date')->nullable();
                $table->integer('km_attuali')->default(0);
                $table->string('stato')->default('operativo');
                $table->timestamps();
            });
            $results[] = "Table 'vehicles' created.";
        } else {
            // Add missing columns if they don't exist
            $columns = [
                'immatricolazione_mese' => 'integer',
                'immatricolazione_anno' => 'integer',
                'assicurazione_compagnia' => 'string',
                'assicurazione_polizza' => 'string',
                'assicurazione_copertura' => 'date',
                'ultima_revisione' => 'date',
            ];

            foreach ($columns as $column => $type) {
                try {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('vehicles', $column)) {
                        \Illuminate\Support\Facades\Schema::table('vehicles', function ($table) use ($column, $type) {
                            $table->$type($column)->nullable();
                        });
                        $results[] = "Column '$column' added.";
                    }
                } catch (\Exception $e) {
                    $results[] = "Error adding column '$column': " . $e->getMessage();
                }
            }
        }
    } catch (\Exception $e) {
        $results[] = "Error updating 'vehicles': " . $e->getMessage();
    }

    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('vehicle_revisions')) {
            \Illuminate\Support\Facades\Schema::create('vehicle_revisions', function ($table) {
                $table->id();
                $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
                $table->date('data_revisione');
                $table->string('esito')->default('regolare'); // regolare, ripetere, sospeso
                $table->integer('km_rilevati')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
            $results[] = "Table 'vehicle_revisions' created.";
        }
    } catch (\Exception $e) {
        $results[] = "Error creating 'vehicle_revisions': " . $e->getMessage();
    }

    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('vehicle_logs')) {
            \Illuminate\Support\Facades\Schema::create('vehicle_logs', function ($table) {
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
            $results[] = "Table 'vehicle_logs' created.";
        } else {
            $results[] = "Table 'vehicle_logs' already exists.";
        }
    } catch (\Exception $e) {
        $results[] = "Error creating 'vehicle_logs': " . $e->getMessage();
    }

    // Roles and Permissions setup
    try {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'vehicle.full_edit',
            'vehicle.limited_edit',
            'vehicle.assign',
            'vehicle.view_logs'
        ];
        foreach ($permissions as $p) {
            \Spatie\Permission\Models\Permission::findOrCreate($p);
        }

        // Create Roles and assign permissions
        $roleAdmin = \Spatie\Permission\Models\Role::findOrCreate('amministratore di sistema');
        $roleAdmin->givePermissionTo($permissions);

        $roleManager = \Spatie\Permission\Models\Role::findOrCreate('responsabile parco macchine');
        $roleManager->syncPermissions($permissions);

        $roleOperator = \Spatie\Permission\Models\Role::findOrCreate('operatore parco macchine');
        $roleOperator->syncPermissions(['vehicle.limited_edit', 'vehicle.assign', 'vehicle.view_logs']);

        // Give full permissions to super-admin and admin if they exist
        $roleSuper = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
        if ($roleSuper) $roleSuper->givePermissionTo($permissions);
        
        $roleA = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
        if ($roleA) $roleA->givePermissionTo($permissions);

        $results[] = "Ruoli e Permessi Autoparco configurati.";
    } catch (\Exception $e) {
        $results[] = "Error in Roles setup: " . $e->getMessage();
    }

    // Update existing statuses to new terminology
    \Illuminate\Support\Facades\DB::table('vehicles')->where('stato', 'disponibile')->update(['stato' => 'operativo']);

    // Diagnostics: Show columns
    $results[] = "--- Current Database Schema (Vehicles) ---";
    $columns = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM vehicles");
    foreach ($columns as $c) {
        $results[] = "Column: {$c->Field}, Type: {$c->Type}, Null: {$c->Null}";
    }

    $output = "<h1>Sincronizzazione Database</h1><ul>";
    foreach ($results as $r) {
        $output .= "<li>$r</li>";
    }
    $output .= "</ul><a href='/autoparco'>Torna alla Dashboard</a>";

    return $output;
    // Ensure Autoparco is in Dashboard Sections
    try {
        if (!file_exists(public_path('svg'))) {
            mkdir(public_path('svg'), 0755, true);
        }
        if (!file_exists(public_path('svg/sprites.svg'))) {
            file_put_contents(public_path('svg/sprites.svg'), '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg"></svg>');
        }

        \Illuminate\Support\Facades\DB::table('dashboard_sections')->updateOrInsert(
            ['route' => '/autoparco'],
            [
                'title' => 'Autoparco',
                'description' => 'Gestione Parco Macchine e Mezzi',
                'icon' => '🚗',
                'color' => '#2e7d32',
                'stato' => 'operativo',
                'updated_at' => now()
            ]
        );
        // Remove old incorrect entry
        \Illuminate\Support\Facades\DB::table('dashboard_sections')->where('route', 'autoparco')->delete();
        
        $results[] = "Dashboard section 'Autoparco' ensured with correct route.";
    } catch (\Exception $e) {
        $results[] = "Error updating dashboard sections: " . $e->getMessage();
    }

    return implode("<br>", $results) . "<br><br>Please refresh the dashboard.";
});

Route::get('/dashboard', function () {
    $expiringAssicurazione = 0;
    $expiringRevisione = 0;

    try {
        $expiringAssicurazione = \App\Models\Vehicle::where('scadenza_assicurazione', '<=', \Carbon\Carbon::now()->addDays(30))
            ->where('stato', '!=', 'fuori servizio')
            ->count();
        
        $expiringRevisione = \App\Models\Vehicle::where('scadenza_revisione', '<=', \Carbon\Carbon::now()->addDays(30))
            ->where('stato', '!=', 'fuori servizio')
            ->count();
    } catch (\Illuminate\Database\QueryException $e) {
        // Log the error or handle it silently if the table is missing
        \Illuminate\Support\Facades\Log::warning('Dashboard widgets skipped: ' . $e->getMessage());
    }

    return view('dashboard', compact('expiringAssicurazione', 'expiringRevisione'));
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-db', function () {
    try {
        $count = \App\Models\InternalEmployee::count();
        return "Internal Employees in DB: " . $count;
    } catch (\Exception $e) {
        return "Error connecting to DB: " . $e->getMessage();
    }
});

require __DIR__.'/auth.php';

// Location API Routes for Registration Form
use App\Http\Controllers\Api\LocationController;

Route::prefix('api')->group(function () {
    Route::get('/provinces', [LocationController::class, 'getProvinces'])->name('api.provinces');
    Route::get('/cities/{province_id}', [LocationController::class, 'getCities'])->name('api.cities');
    Route::get('/countries', [LocationController::class, 'getCountries'])->name('api.countries');
});

// Admin Routes
use App\Http\Controllers\Admin\SmtpSettingsController;
use App\Http\Controllers\Admin\SiteSettingsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SectionManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AIArchitectController;
use App\Http\Controllers\Admin\FiscalCodeToolsController;
use App\Http\Controllers\ProtezioneCivileController;
use App\Http\Controllers\AibStationController;
use App\Http\Controllers\AibTeamController;
use App\Http\Controllers\IncidentController;

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {


    // Admin index
    Route::get('/', function () {
        return view('admin.index');
    })->name('index');

    // AI Architect
    Route::get('/ai/architect', [AIArchitectController::class, 'index'])->name('ai.architect');
    Route::post('/ai/audit', [AIArchitectController::class, 'runAudit'])->name('ai.audit');

    // Fiscal Code Tools
    Route::get('/tools/fiscal-code', [FiscalCodeToolsController::class, 'index'])->name('tools.fiscal_code.index');
    Route::post('/tools/fiscal-code/calculate', [FiscalCodeToolsController::class, 'calculate'])->name('tools.fiscal_code.calculate');
    Route::post('/tools/fiscal-code/reverse', [FiscalCodeToolsController::class, 'reverse'])->name('tools.fiscal_code.reverse');

    // Logs
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');

    // Site Settings
    Route::get('/settings', [SiteSettingsController::class, 'index'])->name('site.index');
    Route::put('/settings', [SiteSettingsController::class, 'update'])->name('site.update');

    // SMTP
    Route::get('/smtp', [SmtpSettingsController::class, 'index'])->name('smtp.index');
    Route::put('/smtp', [SmtpSettingsController::class, 'update'])->name('smtp.update');
    Route::post('/smtp/test', [SmtpSettingsController::class, 'test'])->name('smtp.test');

    // User Management
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [UserManagementController::class, 'reject'])->name('users.reject');
    Route::post('/users/{id}/reintegrate', [UserManagementController::class, 'reintegrate'])->name('users.reintegrate');
    Route::post('/users/{id}/role', [UserManagementController::class, 'assignRole'])->name('users.assignRole');
    Route::delete('/users/{id}/role', [UserManagementController::class, 'removeRole'])->name('users.removeRole');
    Route::post('/users/{id}/toggle-type', [UserManagementController::class, 'toggleType'])->name('users.toggleType');
    Route::post('/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::delete('/users/{id}', [UserManagementController::class, 'delete'])->name('users.delete');
    Route::get('/users/search-employees', [UserManagementController::class, 'searchEmployees'])->name('users.searchEmployees');
    Route::get('/users/employee-detail/{type}/{id}', [UserManagementController::class, 'employeeDetail'])->name('users.employeeDetail');
    Route::post('/users/create-from-employee', [UserManagementController::class, 'createFromEmployee'])->name('users.createFromEmployee');
    Route::post('/users/{id}/area-role', [UserManagementController::class, 'addAreaRole'])->name('users.addAreaRole');
    Route::delete('/users/area-role/{areaRoleId}', [UserManagementController::class, 'removeAreaRole'])->name('users.removeAreaRole');

    // Section Management
    Route::get('/sections', [SectionManagementController::class, 'index'])->name('sections.index');
    Route::post('/sections', [SectionManagementController::class, 'store'])->name('sections.store');
    Route::put('/sections/{id}', [SectionManagementController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{id}', [SectionManagementController::class, 'destroy'])->name('sections.destroy');

    // Style
    Route::get('/style', function () {
        return view('admin.style.index');
    })->name('style.index');
});

// HR (Risorse Umane) Routes
use App\Http\Controllers\HRController;

Route::middleware(['auth', 'hr.access'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/', [HRController::class, 'index'])->name('index');

    // Internal employees
    Route::get('/internal', [HRController::class, 'internalEmployees'])->name('internal.index');
    Route::get('/internal/{id}', [HRController::class, 'showInternal'])->name('internal.show');
    Route::put('/internal/{id}', [HRController::class, 'updateInternal'])->name('internal.update');
    Route::post('/internal/bulk-update', [HRController::class, 'bulkUpdateInternal'])->name('internal.bulkUpdate');
    Route::post('/internal/{id}/area-role', [HRController::class, 'addEmployeeAreaRole'])->name('internal.addAreaRole');
    Route::delete('/internal/{id}/area-role/{areaRoleId}', [HRController::class, 'removeEmployeeAreaRole'])->name('internal.removeAreaRole');

    // External employees
    Route::get('/external', [HRController::class, 'externalEmployees'])->name('external.index');
    Route::get('/external/create', [HRController::class, 'createExternal'])->name('external.create');
    Route::post('/external', [HRController::class, 'storeExternal'])->name('external.store');
    Route::get('/external/{id}', [HRController::class, 'showExternal'])->name('external.show');
    Route::put('/external/{id}', [HRController::class, 'updateExternal'])->name('external.update');

    // Export
    Route::get('/export', [HRController::class, 'exportFiltered'])->name('export');
});

// Autoparco (Parco Macchine) Routes
use App\Http\Controllers\AutoparcoController;
Route::middleware(['auth'])->prefix('autoparco')->name('autoparco.')->group(function () {
    Route::get('/', [AutoparcoController::class, 'index'])->name('index');
    Route::post('/', [AutoparcoController::class, 'store'])->name('store');
    Route::get('/{vehicle}', [AutoparcoController::class, 'show'])->name('show');
    Route::get('/{vehicle}/logs', [AutoparcoController::class, 'getLogs'])->name('logs');
    Route::post('/{vehicle}/assign', [AutoparcoController::class, 'assign'])->name('assign');
    Route::post('/{vehicle}/return', [AutoparcoController::class, 'returnVehicle'])->name('return');
    Route::post('/{vehicle}/status', [AutoparcoController::class, 'updateStatus'])->name('updateStatus');
    Route::post('/{vehicle}/revision', [AutoparcoController::class, 'addRevision'])->name('addRevision');
    Route::put('/{vehicle}', [AutoparcoController::class, 'update'])->name('update');

    // Certifications
    Route::get('/certifications/{userId}', [AutoparcoController::class, 'getCertifications'])->name('certifications.get');
    Route::post('/certifications', [AutoparcoController::class, 'storeCertification'])->name('certifications.store');
    Route::delete('/certifications/{id}', [AutoparcoController::class, 'deleteCertification'])->name('certifications.delete');
});

// Protezione Civile Routes
Route::middleware(['auth'])->prefix('pc')->name('pc.')->group(function () {
    Route::get('/', [ProtezioneCivileController::class, 'index'])->name('index');

    // AIB (Antincendio Boschivo)
    Route::prefix('aib')->name('aib.')->group(function () {
        Route::get('/postazioni', [\App\Http\Controllers\LocationController::class, 'index'])->name('locations.index');
        Route::post('/sedi', [\App\Http\Controllers\LocationController::class, 'storeLocation'])->name('locations.store');
        Route::put('/sedi/{location}', [\App\Http\Controllers\LocationController::class, 'updateLocation'])->name('locations.update');
        Route::delete('/sedi/{location}', [\App\Http\Controllers\LocationController::class, 'destroyLocation'])->name('locations.destroy');
        
        Route::post('/sedi/{location}/postazioni', [\App\Http\Controllers\LocationController::class, 'storeStation'])->name('stations.store');
        Route::put('/postazioni/{station}', [\App\Http\Controllers\LocationController::class, 'updateStation'])->name('stations.update');
        Route::delete('/postazioni/{station}', [\App\Http\Controllers\LocationController::class, 'destroyStation'])->name('stations.destroy');
        
        Route::get('/squadre', [AibTeamController::class, 'index'])->name('teams.index');
        Route::get('/squadre/nuova', [AibTeamController::class, 'create'])->name('teams.create');
        Route::post('/squadre', [AibTeamController::class, 'store'])->name('teams.store');
        
        Route::get('/telefoni', [\App\Http\Controllers\CompanyPhoneController::class, 'index'])->name('phones.index');
        Route::post('/telefoni', [\App\Http\Controllers\CompanyPhoneController::class, 'store'])->name('phones.store');
        Route::put('/telefoni/{phone}', [\App\Http\Controllers\CompanyPhoneController::class, 'update'])->name('phones.update');
        Route::delete('/telefoni/{phone}', [\App\Http\Controllers\CompanyPhoneController::class, 'destroy'])->name('phones.destroy');
        
        Route::get('/personale', [\App\Http\Controllers\EmployeeAibController::class, 'index'])->name('personnel.index');
        Route::post('/personale/{employee}/toggle-aib', [\App\Http\Controllers\EmployeeAibController::class, 'toggleAib'])->name('personnel.toggleAib');
    });

    // Emergenze (Protezione Civile)
    Route::get('/emergenze', [IncidentController::class, 'index'])->name('emergencies.index');
    Route::get('/emergenze/import', [IncidentController::class, 'importForm'])->name('emergencies.import');
    Route::post('/emergenze/import', [IncidentController::class, 'import'])->name('emergencies.import.post');
});


