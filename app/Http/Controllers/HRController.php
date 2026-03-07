<?php

namespace App\Http\Controllers;

use App\Models\InternalEmployee;
use App\Models\ExternalEmployee;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HRController extends Controller
{
    /**
     * Check if current user can edit HR data.
     * Edit allowed for: super-admin, or users with area 'Risorse Umane' and role 'responsabile' or 'operatore'.
     */
    private function canEdit(): bool
    {
        $user = Auth::user();

        if ($user && $user->hasRole('super-admin')) {
            return true;
        }

        return $user && DB::table('user_area_roles')
            ->where('user_id', $user->id)
            ->where('area', 'Risorse Umane')
            ->whereIn('role', ['responsabile', 'operatore'])
            ->exists();
    }

    /**
     * Abort 403 if user cannot edit.
     */
    private function authorizeEdit(): void
    {
        if (!$this->canEdit()) {
            abort(403, 'Non hai i permessi per modificare i dati nella sezione Risorse Umane.');
        }
    }

    /**
     * HR main index page
     */
    public function index()
    {
        $stats = [
            'total_internal' => DB::table('internal_employees')->count(),
            'active_internal' => DB::table('internal_employees')->where('status', 'active')->count(),
            'suspended_internal' => DB::table('internal_employees')->where('status', 'suspended')->count(),
            'terminated_internal' => DB::table('internal_employees')->where('status', 'terminated')->count(),
            'total_external' => DB::table('external_employees')->count(),
            'aib_qualified' => DB::table('internal_employees')->where('is_aib_qualified', true)->count(),
        ];

        $canEdit = $this->canEdit();

        \App\Services\ActivityLogger::log('view', null, null, "Accesso alla sezione Risorse Umane (Anagrafica)");

        return view('hr.index', compact('stats', 'canEdit'));
    }

    /**
     * Internal employees list with filters and pagination
     */
    public function internalEmployees(Request $request)
    {
        $query = InternalEmployee::query()->orderBy('last_name')->orderBy('first_name');

        // Filters
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'LIKE', "%{$s}%")
                  ->orWhere('last_name', 'LIKE', "%{$s}%")
                  ->orWhere('tax_code', 'LIKE', "%{$s}%")
                  ->orWhere('badge_number', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('is_aib_qualified')) {
            $query->where('is_aib_qualified', $request->is_aib_qualified);
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        $perPage = in_array((int) $request->get('per_page', 30), [30, 50, 100, 200])
            ? (int) $request->get('per_page', 30)
            : 30;

        $employees = $query->paginate($perPage)->withQueryString();

        $statusCounts = [
            'all' => InternalEmployee::count(),
            'active' => InternalEmployee::where('status', 'active')->count(),
            'suspended' => InternalEmployee::where('status', 'suspended')->count(),
            'terminated' => InternalEmployee::where('status', 'terminated')->count(),
            'pending' => InternalEmployee::where('status', 'pending')->count(),
        ];

        $canEdit = $this->canEdit();

        \App\Services\ActivityLogger::log('view', null, null, "Consultazione elenco Anagrafica Personale (Filtri: " . json_encode($request->all()) . ")");

        return view('hr.internal.index', compact('employees', 'statusCounts', 'canEdit'));
    }

    /**
     * Show internal employee detail / personal file
     */
    public function showInternal($id)
    {
        $employee = InternalEmployee::findOrFail($id);
        $canEdit = $this->canEdit();

        // Find linked user via fiscal_code <-> tax_code
        $linkedUser = User::where('fiscal_code', $employee->tax_code)->first();
        $areaRoles = collect();
        if ($linkedUser) {
            $areaRoles = DB::table('user_area_roles')
                ->where('user_id', $linkedUser->id)
                ->orderBy('area')
                ->get();
        }

        // Load available areas from system_areas
        $availableAreas = DB::table('system_areas')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name');

        $availableRoles = ['responsabile', 'operatore', 'viewer'];

        \App\Services\ActivityLogger::log('view', 'InternalEmployee', $id, "Consultazione fascicolo personale di: {$employee->first_name} {$employee->last_name}");

        return view('hr.internal.show', compact(
            'employee', 'canEdit', 'linkedUser', 'areaRoles',
            'availableAreas', 'availableRoles'
        ));
    }

    /**
     * Update internal employee
     */
    public function updateInternal(Request $request, $id)
    {
        $this->authorizeEdit();

        $employee = InternalEmployee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tax_code' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'badge_number' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'employee_type' => 'required|in:internal,external',
            'status' => 'required|in:active,terminated,suspended,pending',
            'email' => 'nullable|email|max:255',
            'personal_email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'personal_phone' => 'nullable|string|max:255',
            'is_aib_qualified' => 'boolean',
            'is_emergency_available' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $employee->fill($validated);
        $dirty = $employee->getDirty();
        $changes = [];

        $labels = [
            'first_name' => 'Nome',
            'last_name' => 'Cognome',
            'tax_code' => 'Codice Fiscale',
            'birth_date' => 'Data di Nascita',
            'birth_place' => 'Luogo di Nascita',
            'gender' => 'Genere',
            'badge_number' => 'Matricola',
            'position' => 'Mansione',
            'employee_type' => 'Tipo Contratto',
            'status' => 'Stato Ops',
            'email' => 'Email Aziendale',
            'personal_email' => 'Email Personale',
            'phone' => 'Telefono',
            'personal_phone' => 'Cellulare',
            'is_aib_qualified' => 'Qualifica AIB',
            'is_emergency_available' => 'Disp. Emergenza',
            'notes' => 'Note/Mansione',
        ];

        foreach ($dirty as $key => $value) {
            $label = $labels[$key] ?? $key;
            $oldValue = $employee->getOriginal($key);

            // Format values for human readability
            $fmt = function($v) {
                if ($v === true || $v === 1 || $v === '1') return 'Sì';
                if ($v === false || $v === 0 || $v === '0') return 'No';
                return $v ?? 'N/D';
            };

            $changes[] = "{$label}: [" . $fmt($oldValue) . " → " . $fmt($value) . "]";
        }

        $employee->save();

        if (count($changes) > 0) {
            \App\Services\ActivityLogger::log(
                'update',
                'InternalEmployee',
                $id,
                "Modificati dati dipendente {$employee->first_name} {$employee->last_name}. Variazioni: " . implode("; ", $changes)
            );
        } else {
             \App\Services\ActivityLogger::log('view', 'InternalEmployee', $id, "Accesso in modifica senza variazioni per: {$employee->first_name} {$employee->last_name}");
        }

        return back()->with('success', "Dati di {$employee->first_name} {$employee->last_name} aggiornati.");
    }

    /**
     * Bulk update internal employees
     */
    public function bulkUpdateInternal(Request $request)
    {
        $this->authorizeEdit();

        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'integer|exists:internal_employees,id',
            'field' => 'required|string|in:status,position,employee_type,is_aib_qualified,is_emergency_available',
            'value' => 'required|string',
        ]);

        $field = $request->field;
        $value = $request->value;

        // Type cast for boolean fields
        if (in_array($field, ['is_aib_qualified', 'is_emergency_available'])) {
            $value = $value === '1' || $value === 'true';
        }

        $count = InternalEmployee::whereIn('id', $request->employee_ids)
            ->update([$field => $value, 'updated_at' => now()]);

        $fieldLabels = [
            'status' => 'Stato',
            'position' => 'Posizione',
            'employee_type' => 'Tipo dipendente',
            'is_aib_qualified' => 'Qualifica AIB',
            'is_emergency_available' => 'Disponibilità emergenza',
        ];

        return back()->with('success', "{$count} dipendenti aggiornati — {$fieldLabels[$field]}: {$request->value}");
    }

    /**
     * External employees list
     */
    public function externalEmployees(Request $request)
    {
        $query = ExternalEmployee::with('organization')->orderBy('last_name')->orderBy('first_name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'LIKE', "%{$s}%")
                  ->orWhere('last_name', 'LIKE', "%{$s}%")
                  ->orWhere('tax_code', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('is_aib')) {
            $query->where('is_aib', $request->boolean('is_aib'));
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        $employees = $query->paginate(30)->withQueryString();
        $canEdit = $this->canEdit();
        $organizations = Organization::orderBy('name')->get();

        return view('hr.external.index', compact('employees', 'canEdit', 'organizations'));
    }

    /**
     * Show external employee create form
     */
    public function createExternal()
    {
        $this->authorizeEdit();

        // Sort: "Nessuna Organizzazione" first, then others alphabetically
        $organizations = Organization::orderByRaw("CASE WHEN name = 'Nessuna Organizzazione' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('hr.external.create', compact('organizations'));
    }

    /**
     * Store new external employee
     */
    public function storeExternal(Request $request)
    {
        $this->authorizeEdit();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tax_code' => 'required|string|size:16|unique:external_employees,tax_code',
            'birth_date' => 'required|date',
            'birth_place_text' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'organization_id' => 'required', // Can be numeric ID or "new"
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_aib' => 'boolean',
            // New organization fields
            'new_org_name' => 'required_if:organization_id,new|nullable|string|max:255',
            'new_org_type' => 'required_if:organization_id,new|nullable|in:public,private',
            'new_org_tax_code' => 'nullable|string|max:255',
            'new_org_is_aib' => 'boolean',
        ]);

        $validated['is_aib'] = $request->boolean('is_aib');

        // Handle on-the-fly organization creation
        if ($request->organization_id === 'new') {
            $org = Organization::create([
                'name' => $validated['new_org_name'],
                'type' => $validated['new_org_type'],
                'tax_code' => $validated['new_org_tax_code'],
                'is_aib' => $request->boolean('new_org_is_aib'),
            ]);
            $validated['organization_id'] = $org->id;
            if ($org->is_aib) {
                $validated['is_aib'] = true;
            }
        } else {
            // Existing organization
            $org = Organization::findOrFail($request->organization_id);
            if ($org && $org->is_aib) {
                $validated['is_aib'] = true;
            }
        }

        $employee = ExternalEmployee::create($validated);

        \App\Services\ActivityLogger::log(
            'create',
            'ExternalEmployee',
            $employee->id,
            "Creato nuovo collaboratore esterno: {$employee->first_name} {$employee->last_name} (CF: {$employee->tax_code})"
        );

        return redirect()->route('hr.external.index')->with('success', 'Collaboratore registrato con successo.');
    }

    /**
     * Show external employee detail
     */
    public function showExternal($id)
    {
        $employee = ExternalEmployee::with('organization')->findOrFail($id);
        $canEdit = $this->canEdit();

        // Sort: "Nessuna Organizzazione" first, then others alphabetically
        $organizations = Organization::orderByRaw("CASE WHEN name = 'Nessuna Organizzazione' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        return view('hr.external.show', compact('employee', 'canEdit', 'organizations'));
    }

    /**
     * Update external employee
     */
    public function updateExternal(Request $request, $id)
    {
        $this->authorizeEdit();

        $employee = ExternalEmployee::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'tax_code' => 'required|string|size:16|unique:external_employees,tax_code,'.$id,
            'birth_date' => 'required|date',
            'birth_place_text' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'organization_id' => 'required', // numeric ID or "new"
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_aib' => 'boolean',
            // New organization fields (only if relevant, though usually on create only)
            'new_org_name' => 'required_if:organization_id,new|nullable|string|max:255',
            'new_org_type' => 'required_if:organization_id,new|nullable|in:public,private',
            'new_org_tax_code' => 'nullable|string|max:255',
            'new_org_is_aib' => 'boolean',
        ]);

        $validated['is_aib'] = $request->boolean('is_aib');

        if ($request->organization_id === 'new') {
            $org = Organization::create([
                'name' => $validated['new_org_name'],
                'type' => $validated['new_org_type'],
                'tax_code' => $validated['new_org_tax_code'],
                'is_aib' => $request->boolean('new_org_is_aib'),
            ]);
            $validated['organization_id'] = $org->id;
            if ($org->is_aib) {
                $validated['is_aib'] = true;
            }
        } else {
            $org = Organization::findOrFail($request->organization_id);
            if ($org && $org->is_aib) {
                $validated['is_aib'] = true;
            }
        }

        $employee->update($validated);

        return back()->with('success', "Dati di {$employee->first_name} {$employee->last_name} aggiornati.");
    }

    /**
     * Export filtered internal employees as CSV
     */
    public function exportFiltered(Request $request)
    {
        $query = InternalEmployee::query()->orderBy('last_name')->orderBy('first_name');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('employee_type')) {
            $query->where('employee_type', $request->employee_type);
        }
        if ($request->filled('is_aib_qualified')) {
            $query->where('is_aib_qualified', $request->is_aib_qualified);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'LIKE', "%{$s}%")
                  ->orWhere('last_name', 'LIKE', "%{$s}%")
                  ->orWhere('tax_code', 'LIKE', "%{$s}%");
            });
        }

        $employees = $query->get();

        $filename = 'dipendenti_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($employees) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Cognome', 'Nome', 'Codice Fiscale', 'Data Nascita', 'Luogo Nascita',
                'Sesso', 'Badge', 'Posizione', 'Tipo', 'Stato',
                'Email', 'Email Personale', 'Telefono', 'Telefono Personale',
                'Qualificato AIB', 'Disponibile Emergenza', 'Note',
            ], ';');

            foreach ($employees as $e) {
                fputcsv($file, [
                    $e->last_name, $e->first_name, $e->tax_code,
                    $e->birth_date ? $e->birth_date->format('d/m/Y') : '',
                    $e->birth_place,
                    $e->gender === 'male' ? 'M' : ($e->gender === 'female' ? 'F' : ''),
                    $e->badge_number, $e->position, $e->employee_type, $e->status,
                    $e->email, $e->personal_email, $e->phone, $e->personal_phone,
                    $e->is_aib_qualified ? 'Sì' : 'No',
                    $e->is_emergency_available ? 'Sì' : 'No',
                    $e->notes,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Add an area-role assignment to the linked user of an internal employee.
     */
    public function addEmployeeAreaRole(Request $request, $employeeId)
    {
        $this->authorizeEdit();

        $request->validate([
            'role' => 'required|string|in:responsabile,operatore,viewer',
            'area' => 'required|string',
        ]);

        $employee = InternalEmployee::findOrFail($employeeId);
        $linkedUser = User::where('fiscal_code', $employee->tax_code)->first();

        if (!$linkedUser) {
            return back()->with('error', 'Questo dipendente non ha un account utente collegato. Creare prima un account utente.');
        }

        // Check for duplicate
        $exists = DB::table('user_area_roles')
            ->where('user_id', $linkedUser->id)
            ->where('role', $request->role)
            ->where('area', $request->area)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Questa assegnazione area/ruolo esiste già.');
        }

        DB::table('user_area_roles')->insert([
            'user_id' => $linkedUser->id,
            'role' => $request->role,
            'area' => $request->area,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \App\Services\ActivityLogger::logSecurity("Assegnato ruolo '{$request->role}' per area '{$request->area}' al dipendente interno ID: {$employeeId}");

        return back()->with('success', 'Area/Ruolo aggiunto con successo.');
    }

    /**
     * Remove an area-role assignment.
     */
    public function removeEmployeeAreaRole($employeeId, $areaRoleId)
    {
        $this->authorizeEdit();

        $areaRole = DB::table('user_area_roles')->where('id', $areaRoleId)->first();

        if ($areaRole) {
            DB::table('user_area_roles')->where('id', $areaRoleId)->delete();
            \App\Services\ActivityLogger::logSecurity("Rimosso ruolo '{$areaRole->role}' per area '{$areaRole->area}' dal dipendente interno ID: {$employeeId}");
        }

        return back()->with('success', 'Abilitazione rimossa.');
    }

    /**
     * Get all active system areas (for API/AJAX use).
     */
    public function getSystemAreas()
    {
        $areas = DB::table('system_areas')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return response()->json($areas);
    }
}
