<?php

namespace App\Http\Controllers;

use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseLocationController extends Controller
{
    public function index()
    {
        // Load root locations (no parent) with recursive children
        $roots = WarehouseLocation::whereNull('parent_id')
                                   ->with('children.children.children.children')
                                   ->orderBy('name')
                                   ->get();
        $total = WarehouseLocation::count();
        return view('magazzino.locations.index', compact('roots', 'total'));
    }

    public function create(Request $request)
    {
        $allLocations = WarehouseLocation::orderBy('name')->get();
        $parentId = $request->query('parent_id');
        return view('magazzino.locations.form', compact('allLocations', 'parentId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => 'required|in:sede_centrale,hub_centrale,magazzino_centrale,distretto,hub_distretto,magazzino_distretto,distaccamento,magazzino_locale,punto_consumo',
            'parent_id' => 'nullable|exists:warehouse_locations,id',
        ]);

        WarehouseLocation::create($validated);

        return redirect()->route('magazzino.locations.index')
                         ->with('success', "Ubicazione '{$validated['name']}' creata con successo.");
    }

    public function edit(WarehouseLocation $location)
    {
        $allLocations = WarehouseLocation::where('id', '!=', $location->id)->orderBy('name')->get();
        return view('magazzino.locations.form', compact('location', 'allLocations'));
    }

    public function update(Request $request, WarehouseLocation $location)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => 'required|in:sede_centrale,hub_centrale,magazzino_centrale,distretto,hub_distretto,magazzino_distretto,distaccamento,magazzino_locale,punto_consumo',
            'parent_id' => 'nullable|exists:warehouse_locations,id',
        ]);

        $location->update($validated);

        return redirect()->route('magazzino.locations.index')
                         ->with('success', "Ubicazione '{$validated['name']}' aggiornata.");
    }

    public function destroy(WarehouseLocation $location)
    {
        if ($location->children()->count() > 0) {
            return back()->with('error', "Impossibile eliminare: questa ubicazione ha {$location->children()->count()} sotto-ubicazioni collegate. Eliminalele prima.");
        }
        $name = $location->name;
        $location->delete();
        return redirect()->route('magazzino.locations.index')
                         ->with('success', "Ubicazione '{$name}' eliminata.");
    }
}
