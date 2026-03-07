<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\AibStation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\ActivityLogger;

class LocationController extends Controller
{
    /**
     * Proxy Nominatim requests to bypass CORS and User-Agent blocks.
     */
    public function proxynominate(Request $request)
    {
        $url = $request->input('url');
        if (!$url || !str_starts_with($url, 'https://nominatim.openstreetmap.org/')) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }

        try {
            // Forward the query string exactly
            $queryString = request()->getQueryString();
            // Remove 'url=' from the query string
            $queryString = preg_replace('/url=[^&]*&?/', '', $queryString);
            
            $finalUrl = $url . (str_contains($url, '?') ? '&' : '?') . $queryString;
            
            $response = Http::withHeaders([
                'User-Agent' => 'GestionaleCV/1.0 (info@gestionalecv.test)', // Nominatim requires a valid UA
                'Accept-Language' => 'it-IT,it;q=0.9,en-US;q=0.8,en;q=0.7'
            ])->timeout(10)->get($finalUrl);

            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json(['error' => 'API Request Failed: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $query = Location::with('aibStations');

        if ($request->filled('tipo_sede')) {
            $query->where('tipo_sede', $request->tipo_sede);
        }
        if ($request->filled('provincia')) {
            $query->where('provincia', $request->provincia);
        }
        if ($request->filled('citta')) {
            $query->where('citta', 'like', '%' . $request->citta . '%');
        }
        if ($request->has('has_aib_stations')) {
            $query->has('aibStations');
        }

        $locations = $query->get();
        $provinces = \App\Models\Location\Province::orderBy('name')->get();

        return view('pc.aib.locations.index', compact('locations', 'provinces'));
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_sede' => 'required|string|max:255',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:255',
            'localita' => 'nullable|string|max:255',
            'provincia' => 'required|string|size:2',
            'cap' => 'nullable|string|max:10',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'note_operative' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $location = Location::create($validated);
        
        ActivityLogger::log('create', 'Location', $location->id, "Sede aziendale creata: {$location->nome}");

        return to_route('pc.aib.locations.index')->with('success', 'Sede creata con successo.');
    }

    public function updateLocation(Request $request, Location $location)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo_sede' => 'required|string|max:255',
            'indirizzo' => 'nullable|string|max:255',
            'citta' => 'nullable|string|max:255',
            'localita' => 'nullable|string|max:255',
            'provincia' => 'required|string|size:2',
            'cap' => 'nullable|string|max:10',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'note_operative' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);
        
        ActivityLogger::log('update', 'Location', $location->id, "Sede aziendale aggiornata: {$location->nome}");

        return to_route('pc.aib.locations.index')->with('success', 'Sede aggiornata con successo.');
    }

    public function destroyLocation(Location $location)
    {
        ActivityLogger::log('delete', 'Location', $location->id, "Sede aziendale eliminata: {$location->nome}");
        $location->delete();
        return to_route('pc.aib.locations.index')->with('success', 'Sede eliminata con successo.');
    }

    // AIB Station Methods
    public function storeStation(Request $request, Location $location)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:Base Operativa,Vedetta,Eliporto,Altro',
            'localita' => 'nullable|string|max:255',
            'comune' => 'nullable|string|max:255',
            'latitudine' => 'nullable|numeric',
            'longitudine' => 'nullable|numeric',
            'lat_dms' => 'nullable|string|max:255',
            'lon_dms' => 'nullable|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo,In Manutenzione',
            'note' => 'nullable|string',
        ]);

        $validated['location_id'] = $location->id;
        $validated['provincia'] = $location->provincia;
        $validated['comune'] = $validated['comune'] ?? $location->citta;
        
        $station = AibStation::create($validated);
        
        ActivityLogger::log('create', 'AibStation', $station->id, "Postazione AIB collegata: {$station->nome} (Sede: {$location->nome})");

        return to_route('pc.aib.locations.index')->with('success', 'Postazione AIB collegata con successo alla sede.');
    }
    
    public function updateStation(Request $request, AibStation $station)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:Base Operativa,Vedetta,Eliporto,Altro',
            'localita' => 'nullable|string|max:255',
            'comune' => 'nullable|string|max:255',
            'latitudine' => 'nullable|numeric',
            'longitudine' => 'nullable|numeric',
            'lat_dms' => 'nullable|string|max:255',
            'lon_dms' => 'nullable|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo,In Manutenzione',
            'note' => 'nullable|string',
        ]);

        $station->update($validated);
        
        ActivityLogger::log('update', 'AibStation', $station->id, "Postazione AIB aggiornata: {$station->nome}");

        return to_route('pc.aib.locations.index')->with('success', 'Postazione AIB aggiornata con successo.');
    }

    public function destroyStation(AibStation $station)
    {
        ActivityLogger::log('delete', 'AibStation', $station->id, "Postazione AIB eliminata: {$station->nome}");
        $station->delete();
        return to_route('pc.aib.locations.index')->with('success', 'Postazione AIB eliminata con successo.');
    }
}
