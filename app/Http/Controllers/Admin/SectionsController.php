<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardSection;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // Optionally: $this->middleware('role:super-admin');
    }

    public function index()
    {
        // Load all roots with recursive children for tree display
        $tree = DashboardSection::with('childrenRecursive')
            ->roots()
            ->get();

        // Flat list for parent dropdown (all sections that can be parents, i.e. level < 4)
        $potentialParents = DashboardSection::where('level', '<', 4)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        return view('admin.sections.index', compact('tree', 'potentialParents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id'     => 'nullable|exists:dashboard_sections,id',
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:10',
            'color'         => 'nullable|string|max:20',
            'route'         => 'nullable|string|max:255',
            'required_role' => 'nullable|string|max:100',
            'required_area' => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
            'sort_order'    => 'nullable|integer',
        ]);

        // Auto-calculate level from parent
        if (!empty($data['parent_id'])) {
            $parent = DashboardSection::findOrFail($data['parent_id']);
            $data['level'] = min(4, $parent->level + 1);
        } else {
            $data['level'] = 1;
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['icon']      = $data['icon'] ?? '📁';
        $data['color']     = $data['color'] ?? '#0066cc';
        $data['sort_order'] = $data['sort_order'] ?? 0;

        DashboardSection::create($data);

        return back()->with('success', "Voce \"{$data['title']}\" (L{$data['level']}) creata con successo.");
    }

    public function update(Request $request, DashboardSection $section)
    {
        $data = $request->validate([
            'parent_id'     => 'nullable|exists:dashboard_sections,id',
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:10',
            'color'         => 'nullable|string|max:20',
            'route'         => 'nullable|string|max:255',
            'required_role' => 'nullable|string|max:100',
            'required_area' => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',
            'sort_order'    => 'nullable|integer',
        ]);

        // Prevent a section from becoming its own ancestor (simple check)
        if (!empty($data['parent_id']) && $data['parent_id'] == $section->id) {
            return back()->withErrors(['parent_id' => 'Una voce non può essere figlia di se stessa.']);
        }

        // Recalculate level
        if (!empty($data['parent_id'])) {
            $parent = DashboardSection::findOrFail($data['parent_id']);
            $data['level'] = min(4, $parent->level + 1);
        } else {
            $data['level'] = 1;
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $section->update($data);

        return back()->with('success', "Voce \"{$section->title}\" aggiornata.");
    }

    public function destroy(DashboardSection $section)
    {
        $title = $section->title;
        // Children cascade via FK onDelete('cascade')
        $section->delete();

        return back()->with('success', "Voce \"{$title}\" e tutti i suoi figli eliminati.");
    }

    public function toggle(DashboardSection $section)
    {
        $section->update(['is_active' => !$section->is_active]);
        return back()->with('success', "Voce \"{$section->title}\" " . ($section->is_active ? 'disattivata' : 'attivata') . '.');
    }
}
