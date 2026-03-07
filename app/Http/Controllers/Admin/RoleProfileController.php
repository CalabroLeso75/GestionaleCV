<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleProfileController extends Controller
{
    public function index()
    {
        // All Spatie roles with user count
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                $role->section_ids = DB::table('role_sections')
                    ->where('role_id', $role->id)
                    ->pluck('dashboard_section_id')
                    ->toArray();
                return $role;
            });

        // All sections flat (for the assignment picker)
        $allSections = DashboardSection::where('is_active', 1)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        // Role hierarchy info
        $hierarchy = DashboardSection::ROLE_HIERARCHY;

        return view('admin.profiles.index', compact('roles', 'allSections', 'hierarchy'));
    }

    /**
     * Assign a dashboard section to a role (add to package).
     */
    public function assignSection(Request $request, $roleId)
    {
        $request->validate(['section_id' => 'required|exists:dashboard_sections,id']);

        $role = Role::findOrFail($roleId);

        // Prevent duplicates (unique constraint handles it, but let's check)
        $exists = DB::table('role_sections')
            ->where('role_id', $roleId)
            ->where('dashboard_section_id', $request->section_id)
            ->exists();

        if (!$exists) {
            DB::table('role_sections')->insert([
                'role_id'              => $roleId,
                'dashboard_section_id' => $request->section_id,
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Sezione aggiunta al profilo ' . $role->name]);
    }

    /**
     * Remove a section from a role's package.
     */
    public function removeSection($roleId, $sectionId)
    {
        DB::table('role_sections')
            ->where('role_id', $roleId)
            ->where('dashboard_section_id', $sectionId)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Sync (replace) all sections for a role.
     */
    public function syncSections(Request $request, $roleId)
    {
        $request->validate(['section_ids' => 'required|array', 'section_ids.*' => 'exists:dashboard_sections,id']);

        $role = Role::findOrFail($roleId);

        // Delete existing
        DB::table('role_sections')->where('role_id', $roleId)->delete();

        // Insert new
        $rows = array_map(fn($sid) => [
            'role_id'              => $roleId,
            'dashboard_section_id' => $sid,
            'created_at'           => now(),
            'updated_at'           => now(),
        ], $request->section_ids);

        if (!empty($rows)) {
            DB::table('role_sections')->insert($rows);
        }

        return back()->with('success', "Pacchetto sezioni per il ruolo \"{$role->name}\" aggiornato.");
    }
}
