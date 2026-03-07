<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Get available areas from system_areas table (dynamic).
     */
    private static function getAreas(): array
    {
        return DB::table('system_areas')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        if ($filter === 'rejected') {
            $rejected = DB::table('rejected_users')
                ->whereNull('reintegrated_at')
                ->orderByDesc('rejected_at')
                ->paginate(20);

            $users = collect();
            $roles = Role::all();
            $counts = $this->getCounts();

            return view('admin.users.index', compact('users', 'roles', 'filter', 'counts', 'rejected'));
        }

        $query = User::with('areaRoles')->orderBy('surname')->orderBy('name');

        switch ($filter) {
            case 'pending':
                $query->where('status', 'pending');
                break;
            case 'active':
                $query->where('status', 'active');
                break;
            case 'suspended':
                $query->where('status', 'suspended');
                break;
            case 'norole':
                $query->where('status', 'active')
                      ->whereDoesntHave('roles');
                break;
        }

        $users = $query->paginate(20);
        $roles = Role::all();
        $counts = $this->getCounts();
        $rejected = collect();
        $areas = self::getAreas();

        return view('admin.users.index', compact('users', 'roles', 'filter', 'counts', 'rejected', 'areas'));
    }

    private function getCounts()
    {
        return [
            'all' => User::count(),
            'pending' => User::where('status', 'pending')->count(),
            'active' => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'norole' => User::where('status', 'active')->whereDoesntHave('roles')->count(),
            'rejected' => DB::table('rejected_users')->whereNull('reintegrated_at')->count(),
        ];
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        \App\Services\ActivityLogger::logSecurity("Approvato utente: {$user->name} {$user->surname} ({$user->email})");

        return back()->with('success', "Utente {$user->name} {$user->surname} approvato con successo.");
    }

    public function reject(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Non è possibile rifiutare un super-admin.');
        }

        $this->archiveUser($user, $request->input('reason', 'Rifiutato dall\'amministratore'));
        $fullName = "{$user->name} {$user->surname}";
        $user->delete();

        \App\Services\ActivityLogger::logSecurity("Rifiutato utente: {$fullName} (CF: {$user->fiscal_code}). Motivo: " . ($request->input('reason') ?? 'Non specificato'));

        return back()->with('success', "Utente {$fullName} rifiutato e archiviato.");
    }

    public function delete(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Non è possibile eliminare un super-admin.');
        }

        $this->archiveUser($user, $request->input('reason', 'Eliminato dall\'amministratore'));
        $fullName = "{$user->name} {$user->surname}";
        $user->delete();

        \App\Services\ActivityLogger::logSecurity("Eliminato utente: {$fullName} (ID: {$user->id}). Motivo: " . ($request->input('reason') ?? 'Non specificato'));

        return back()->with('success', "Utente {$fullName} eliminato e archiviato.");
    }

    private function archiveUser(User $user, ?string $reason = null)
    {
        DB::table('rejected_users')->insert([
            'original_user_id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
            'gender' => $user->gender,
            'fiscal_code' => $user->fiscal_code,
            'birth_date' => $user->birth_date,
            'birth_city_id' => $user->birth_city_id,
            'birth_country_id' => $user->birth_country_id,
            'password' => $user->password,
            'type' => $user->type,
            'internal_employee_id' => $user->internal_employee_id,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function reintegrate($id)
    {
        $rejected = DB::table('rejected_users')->where('id', $id)->first();
        if (!$rejected) {
            return back()->with('error', 'Utente archiviato non trovato.');
        }

        $user = User::create([
            'name' => $rejected->name,
            'surname' => $rejected->surname,
            'email' => $rejected->email,
            'gender' => $rejected->gender,
            'fiscal_code' => $rejected->fiscal_code,
            'birth_date' => $rejected->birth_date,
            'birth_city_id' => $rejected->birth_city_id,
            'birth_country_id' => $rejected->birth_country_id,
            'password' => $rejected->password,
            'type' => $rejected->type,
            'internal_employee_id' => $rejected->internal_employee_id,
            'status' => 'active',
        ]);

        DB::table('rejected_users')->where('id', $id)->update([
            'reintegrated_at' => now(),
            'reintegrated_by' => Auth::id(),
            'updated_at' => now(),
        ]);

        \App\Services\ActivityLogger::logSecurity("Reintegrato utente: {$user->name} {$user->surname} (ID: {$user->id})");

        return redirect()->route('admin.users.index')
            ->with('success', "Utente {$user->name} {$user->surname} reintegrato con successo.");
    }

    public function toggleType($id)
    {
        $user = User::findOrFail($id);
        $newType = $user->type === 'internal' ? 'external' : 'internal';

        if ($user->internal_employee_id) {
            if ($user->type === 'internal') {
                $emp = DB::table('internal_employees')->where('id', $user->internal_employee_id)->first();
                if ($emp) {
                    $newId = DB::table('external_employees')->insertGetId([
                        'first_name' => $emp->first_name,
                        'last_name' => $emp->last_name,
                        'tax_code' => $emp->tax_code,
                        'birth_date' => $emp->birth_date,
                        'organization_id' => $emp->organization_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    DB::table('internal_employees')->where('id', $emp->id)->update(['employee_type' => 'external']);
                    $user->internal_employee_id = $newId;
                }
            } else {
                $emp = DB::table('external_employees')->where('id', $user->internal_employee_id)->first();
                if ($emp) {
                    $newId = DB::table('internal_employees')->insertGetId([
                        'first_name' => $emp->first_name,
                        'last_name' => $emp->last_name,
                        'tax_code' => $emp->tax_code,
                        'birth_date' => $emp->birth_date,
                        'employee_type' => 'internal',
                        'status' => 'active',
                        'organization_id' => $emp->organization_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $user->internal_employee_id = $newId;
                }
            }
        }

        $user->type = $newType;
        $user->save();

        $label = $newType === 'internal' ? 'Interno' : 'Esterno';
        \App\Services\ActivityLogger::logSecurity("Cambiato tipo utente per {$user->name} {$user->surname} a: {$label}");
        return back()->with('success', "{$user->name} {$user->surname} cambiato a: {$label}.");
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('super-admin')) {
            return back()->with('error', 'Non è possibile disattivare un super-admin.');
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        $label = $newStatus === 'active' ? 'Attivato' : 'Disattivato';
        \App\Services\ActivityLogger::logSecurity("Cambiato stato utente {$user->name} {$user->surname} a: {$label}");
        return back()->with('success', "{$user->name} {$user->surname}: {$label}.");
    }

    public function assignRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|string|exists:roles,name']);
        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        \App\Services\ActivityLogger::logSecurity("Assegnato ruolo '{$request->role}' all'utente {$user->name} {$user->surname}");

        return back()->with('success', "Ruolo '{$request->role}' assegnato a {$user->name} {$user->surname}.");
    }

    public function removeRole($id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles([]);

        \App\Services\ActivityLogger::logSecurity("Rimossi tutti i ruoli dall'utente {$user->name} {$user->surname}");

        return back()->with('success', "Ruoli rimossi da {$user->name} {$user->surname}.");
    }

    public function toggleDos($id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole('dos')) {
            $user->removeRole('dos');
            \App\Services\ActivityLogger::logSecurity("Rimossa qualifica D.O.S. all'utente {$user->name} {$user->surname}");
            return back()->with('success', "Qualifica D.O.S. disattivata per {$user->name} {$user->surname}.");
        } else {
            $user->assignRole('dos');
            \App\Services\ActivityLogger::logSecurity("Assegnata qualifica D.O.S. all'utente {$user->name} {$user->surname}");
            return back()->with('success', "Qualifica D.O.S. attivata per {$user->name} {$user->surname}.");
        }
    }

    // ===== ADD USER FROM EMPLOYEE REGISTRY =====

    public function searchEmployees(Request $request)
    {
        $q = $request->get('q', '');
        $results = [];

        if (strlen($q) >= 2) {
            // Search internal employees
            $internals = DB::table('internal_employees')
                ->where(function ($query) use ($q) {
                    $query->where('first_name', 'LIKE', "%{$q}%")
                          ->orWhere('last_name', 'LIKE', "%{$q}%")
                          ->orWhere('tax_code', 'LIKE', "%{$q}%");
                })
                ->limit(20)
                ->get();

            foreach ($internals as $emp) {
                // Check if already a user
                $alreadyUser = User::where('internal_employee_id', $emp->id)
                    ->where('type', 'internal')->exists();

                $results[] = [
                    'id' => $emp->id,
                    'type' => 'internal',
                    'first_name' => $emp->first_name,
                    'last_name' => $emp->last_name,
                    'tax_code' => $emp->tax_code,
                    'email' => $emp->email ?? $emp->personal_email ?? '',
                    'already_user' => $alreadyUser,
                ];
            }

            // Search external employees
            $externals = DB::table('external_employees')
                ->where(function ($query) use ($q) {
                    $query->where('first_name', 'LIKE', "%{$q}%")
                          ->orWhere('last_name', 'LIKE', "%{$q}%")
                          ->orWhere('tax_code', 'LIKE', "%{$q}%");
                })
                ->limit(20)
                ->get();

            foreach ($externals as $emp) {
                $alreadyUser = User::where('internal_employee_id', $emp->id)
                    ->where('type', 'external')->exists();

                $results[] = [
                    'id' => $emp->id,
                    'type' => 'external',
                    'first_name' => $emp->first_name,
                    'last_name' => $emp->last_name,
                    'tax_code' => $emp->tax_code,
                    'email' => '',
                    'already_user' => $alreadyUser,
                ];
            }
        }

        return response()->json($results);
    }

    public function employeeDetail($type, $id)
    {
        if ($type === 'internal') {
            $emp = DB::table('internal_employees')->where('id', $id)->first();
        } else {
            $emp = DB::table('external_employees')->where('id', $id)->first();
        }

        if (!$emp) {
            return response()->json(['error' => 'Non trovato'], 404);
        }

        $data = [
            'id' => $emp->id,
            'type' => $type,
            'first_name' => $emp->first_name,
            'last_name' => $emp->last_name,
            'tax_code' => $emp->tax_code ?? '',
            'birth_date' => $emp->birth_date ?? '',
            'gender' => $emp->gender ?? '',
        ];

        if ($type === 'internal') {
            $data['email'] = $emp->email ?? '';
            $data['personal_email'] = $emp->personal_email ?? '';
            $data['phone'] = $emp->phone ?? '';
            $data['position'] = $emp->position ?? '';
            $data['badge_number'] = $emp->badge_number ?? '';
        } else {
            $data['job_title'] = $emp->job_title ?? '';
        }

        return response()->json($data);
    }

    /**
     * Generate a secure random password with 8 characters.
     * Includes uppercase, lowercase, numbers, and special characters.
     */
    private function generateSecurePassword(): string
    {
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specials = '!@#$%&*?';

        // Ensure at least one of each type
        $password = $upper[random_int(0, strlen($upper) - 1)]
                  . $lower[random_int(0, strlen($lower) - 1)]
                  . $numbers[random_int(0, strlen($numbers) - 1)]
                  . $specials[random_int(0, strlen($specials) - 1)];

        // Fill remaining 4 characters from all pools
        $all = $upper . $lower . $numbers . $specials;
        for ($i = 0; $i < 4; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    public function createFromEmployee(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|integer',
            'employee_type' => 'required|in:internal,external',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|exists:roles,name',
            'area' => 'required|string',
            'privilege_level' => 'required|integer|min:1|max:5',
            'status' => 'required|in:active,suspended',
        ]);

        $type = $request->employee_type;
        $empId = $request->employee_id;

        if ($type === 'internal') {
            $emp = DB::table('internal_employees')->where('id', $empId)->first();
        } else {
            $emp = DB::table('external_employees')->where('id', $empId)->first();
        }

        if (!$emp) {
            return back()->with('error', 'Dipendente non trovato.');
        }

        // Generate secure password
        $plainPassword = $this->generateSecurePassword();

        // Determine gender
        $gender = null;
        if (isset($emp->gender)) {
            $gender = $emp->gender;
        }

        // Create user
        $user = User::create([
            'name' => $emp->first_name,
            'surname' => $emp->last_name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'fiscal_code' => $emp->tax_code ?? null,
            'birth_date' => $emp->birth_date ?? null,
            'gender' => $gender,
            'type' => $type,
            'internal_employee_id' => $empId,
            'status' => $request->status,
        ]);

        // Assign role
        $user->assignRole($request->role);

        // Collect all area-roles for the email
        $allAreaRoles = [[
            'role' => $request->role,
            'area' => $request->area,
            'privilege_level' => $request->privilege_level,
        ]];

        // Assign primary area
        DB::table('user_area_roles')->insert([
            'user_id' => $user->id,
            'role' => $request->role,
            'area' => $request->area,
            'privilege_level' => $request->privilege_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add additional area-roles if provided
        if ($request->has('extra_roles')) {
            foreach ($request->extra_roles as $extra) {
                if (!empty($extra['role']) && !empty($extra['area'])) {
                    DB::table('user_area_roles')->insert([
                        'user_id' => $user->id,
                        'role' => $extra['role'],
                        'area' => $extra['area'],
                        'privilege_level' => $extra['privilege_level'] ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    if (!$user->hasRole($extra['role'])) {
                        $user->assignRole($extra['role']);
                    }
                    $allAreaRoles[] = [
                        'role' => $extra['role'],
                        'area' => $extra['area'],
                        'privilege_level' => $extra['privilege_level'] ?? 1,
                    ];
                }
            }
        }

        // Send welcome email with credentials
        $admin = Auth::user();
        $adminName = $admin->name . ' ' . $admin->surname;
        $loginUrl = url('/login');

        try {
            Mail::to($request->email)->send(new WelcomeUserMail(
                $user->name,
                $user->surname,
                $request->email,
                $plainPassword,
                $allAreaRoles,
                $adminName,
                $loginUrl
            ));
            $emailMsg = ' Email con credenziali inviata.';
        } catch (\Exception $e) {
            $emailMsg = ' Attenzione: invio email fallito (' . $e->getMessage() . '). Password generata: ' . $plainPassword;
        }

        \App\Services\ActivityLogger::logSecurity("Creato nuovo utente dall'anagrafica: {$user->name} {$user->surname} ({$user->email}) con ruolo {$request->role} in area {$request->area} (Livello {$request->privilege_level})");

        return redirect()->route('admin.users.index')
            ->with('success', "Utente {$user->name} {$user->surname} creato con successo.{$emailMsg}");
    }

    public function addAreaRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string',
            'area' => 'required|string',
            'privilege_level' => 'required|integer|min:1|max:5',
        ]);

        $user = User::findOrFail($id);

        DB::table('user_area_roles')->insert([
            'user_id' => $user->id,
            'role' => $request->role,
            'area' => $request->area,
            'privilege_level' => $request->privilege_level,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \App\Services\ActivityLogger::logSecurity("Aggiunto ruolo '{$request->role}' per area '{$request->area}' (Livello {$request->privilege_level}) all'utente {$user->name} {$user->surname}");

        return back()->with('success', "Ruolo/Area aggiunto a {$user->name} {$user->surname}.");
    }

    public function removeAreaRole($areaRoleId)
    {
        DB::table('user_area_roles')->where('id', $areaRoleId)->delete();
        \App\Services\ActivityLogger::logSecurity("Rimossa assegnazione area/ruolo (ID: {$areaRoleId})");
        return back()->with('success', 'Assegnazione area/ruolo rimossa.');
    }
}
