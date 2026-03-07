<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardSection;
use Illuminate\Http\Request;

class SectionManagementController extends Controller
{
    public function index()
    {
        $tree = DashboardSection::with('childrenRecursive')
            ->roots()
            ->get();

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

        if (!empty($data['parent_id'])) {
            $parent = DashboardSection::findOrFail($data['parent_id']);
            $data['level'] = min(4, $parent->level + 1);
        } else {
            $data['level'] = 1;
        }

        $data['is_active']   = $request->boolean('is_active', true);
        $data['icon']        = $data['icon'] ?? '📁';
        $data['color']       = $data['color'] ?? '#0d6efd';
        $data['sort_order']  = $data['sort_order'] ?? (DashboardSection::max('sort_order') + 1);

        DashboardSection::create($data);

        return back()->with('success', "Voce \"{$data['title']}\" (Livello {$data['level']}) creata con successo.");
    }

    public function update(Request $request, $id)
    {
        $section = DashboardSection::findOrFail($id);

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

        // Prevent self-reference
        if (!empty($data['parent_id']) && $data['parent_id'] == $section->id) {
            return back()->withErrors(['parent_id' => 'Una voce non può essere figlia di sé stessa.']);
        }

        if (!empty($data['parent_id'])) {
            $parent = DashboardSection::findOrFail($data['parent_id']);
            $data['level'] = min(4, $parent->level + 1);
        } else {
            $data['level'] = 1;
        }

        $data['is_active'] = $request->boolean('is_active', $section->is_active);

        $section->update($data);

        return back()->with('success', "Voce \"{$section->title}\" aggiornata.");
    }

    public function destroy($id)
    {
        $section = DashboardSection::findOrFail($id);
        $title = $section->title;
        $section->delete(); // cascades children

        return back()->with('success', "Voce \"{$title}\" e tutti i figli eliminati.");
    }

    /** Toggle active/inactive */
    public function toggle($id)
    {
        $section = DashboardSection::findOrFail($id);
        $section->update(['is_active' => !$section->is_active]);

        $stato = $section->is_active ? 'disattivata' : 'attivata';
        return back()->with('success', "Voce \"{$section->title}\" {$stato}.");
    }

    /** Drag-and-drop reorder (AJAX) */
    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $id) {
            DashboardSection::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }
}
