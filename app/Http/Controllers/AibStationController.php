<?php

namespace App\Http\Controllers;

use App\Models\AibStation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AibStationController extends Controller
{
    public function index()
    {
        $stations = AibStation::all();
        return view('pc.aib.stations.index', compact('stations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'provincia' => 'required|string|size:2',
            'comune' => 'nullable|string|max:255',
            'latitudine' => 'nullable|numeric',
            'longitudine' => 'nullable|numeric',
            'tipo' => 'required|in:Base Operativa,Vedetta,Eliporto,Altro',
            'note' => 'nullable|string',
        ]);

        if (empty($validated['latitudine']) && $request->filled('lat_dms')) {
            // Logic for DMS to Decimal conversion (to be implemented)
            $validated['latitudine'] = $this->dmsToDecimal($request->lat_dms);
        }
        
        if (empty($validated['longitudine']) && $request->filled('lon_dms')) {
            $validated['longitudine'] = $this->dmsToDecimal($request->lon_dms);
        }

        // Auto-generate DMS for storage if missing
        if ($request->filled('latitudine') && !$request->filled('lat_dms')) {
             $validated['lat_dms'] = $this->decimalToDms($validated['latitudine'], 'lat');
        }
        if ($request->filled('longitudine') && !$request->filled('lon_dms')) {
             $validated['lon_dms'] = $this->decimalToDms($validated['longitudine'], 'lon');
        }

        $station = AibStation::create($validated);

        return redirect()->route('pc.aib.stations.index')->with('success', 'Postazione creata con successo.');
    }

    /**
     * Convert DMS string to decimal.
     * Expected format: "39° 12' 34.5\""
     */
    private function dmsToDecimal($dms)
    {
        if (empty($dms)) return null;
        
        // Basic regex to extract D, M, S
        preg_match('/(\d+)\s*[°°]\s*(\d+)\s*[\'’]\s*(\d+(\.\d+)?)\s*["”]/', $dms, $matches);
        
        if (count($matches) < 4) return null;
        
        $degrees = (float)$matches[1];
        $minutes = (float)$matches[2];
        $seconds = (float)$matches[3];
        
        $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);
        
        // Handle N/S E/W if present in string
        if (stripos($dms, 'S') !== false || stripos($dms, 'W') !== false) {
            $decimal *= -1;
        }
        
        return round($decimal, 8);
    }

    /**
     * Convert decimal to DMS string.
     */
    private function decimalToDms($decimal, $type = 'lat')
    {
        $abs = abs($decimal);
        $degrees = floor($abs);
        $remainder = ($abs - $degrees) * 60;
        $minutes = floor($remainder);
        $seconds = round(($remainder - $minutes) * 60, 2);
        
        $direction = '';
        if ($type === 'lat') {
            $direction = $decimal >= 0 ? 'N' : 'S';
        } else {
            $direction = $decimal >= 0 ? 'E' : 'W';
        }
        
        return sprintf("%s %d° %d' %.1f\"", $direction, $degrees, $minutes, $seconds);
    }
}
