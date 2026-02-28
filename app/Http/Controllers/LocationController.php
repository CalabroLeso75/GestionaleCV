<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\AibStation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('aibStations')->get();
        return view('pc.aib.locations.index', compact('locations'));
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_sede' => 'required|string|max:255',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:255',
            'provincia' => 'required|string|size:2',
            'cap' => 'nullable|string|max:10',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'note_operative' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Location::create($validated);

        return redirect()->route('pc.aib.locations.index')->with('success', 'Sede creata con successo.');
    }

    public function updateLocation(Request $request, Location $location)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_sede' => 'required|string|max:255',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:255',
            'provincia' => 'required|string|size:2',
            'cap' => 'nullable|string|max:10',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'note_operative' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);

        return redirect()->route('pc.aib.locations.index')->with('success', 'Sede aggiornata con successo.');
    }

    public function destroyLocation(Location $location)
    {
        $location->delete();
        return redirect()->route('pc.aib.locations.index')->with('success', 'Sede eliminata con successo.');
    }

    // AIB Station Methods
    public function storeStation(Request $request, Location $location)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:Base Operativa,Vedetta,Eliporto,Altro',
            'lat_dms' => 'nullable|string|max:255',
            'lon_dms' => 'nullable|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo,In Manutenzione',
            'note' => 'nullable|string',
        ]);

        $validated['location_id'] = $location->id;
        $validated['provincia'] = $location->provincia;
        $validated['comune'] = $location->citta;
        
        AibStation::create($validated);

        return redirect()->route('pc.aib.locations.index')->with('success', 'Postazione AIB collegata con successo alla sede.');
    }
    
    public function updateStation(Request $request, AibStation $station)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:Base Operativa,Vedetta,Eliporto,Altro',
            'lat_dms' => 'nullable|string|max:255',
            'lon_dms' => 'nullable|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo,In Manutenzione',
            'note' => 'nullable|string',
        ]);

        $station->update($validated);

        return redirect()->route('pc.aib.locations.index')->with('success', 'Postazione AIB aggiornata con successo.');
    }

    public function destroyStation(AibStation $station)
    {
        $station->delete();
        return redirect()->route('pc.aib.locations.index')->with('success', 'Postazione AIB eliminata con successo.');
    }
}
