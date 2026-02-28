<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardSection;
use Illuminate\Http\Request;

class SectionManagementController extends Controller
{
    public function index()
    {
        $sections = DashboardSection::orderBy('sort_order')->get();
        return view('admin.sections.index', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string|max:10',
            'route' => 'nullable|string|max:255',
            'color' => 'required|string|max:20',
            'required_role' => 'nullable|string|max:255',
        ]);

        $maxOrder = DashboardSection::max('sort_order') ?? 0;

        DashboardSection::create([
            'title' => $request->title,
            'description' => $request->description,
            'icon' => $request->icon,
            'route' => $request->route,
            'color' => $request->color,
            'required_role' => $request->required_role ?: null,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', "Sezione '{$request->title}' creata.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string|max:10',
            'route' => 'nullable|string|max:255',
            'color' => 'required|string|max:20',
            'required_role' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $section = DashboardSection::findOrFail($id);
        $section->update($request->only(['title', 'description', 'icon', 'route', 'color', 'required_role', 'is_active']));

        return back()->with('success', "Sezione '{$request->title}' aggiornata.");
    }

    public function destroy($id)
    {
        $section = DashboardSection::findOrFail($id);
        $title = $section->title;
        $section->delete();

        return back()->with('success', "Sezione '{$title}' eliminata.");
    }

    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $id) {
            DashboardSection::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }
}
