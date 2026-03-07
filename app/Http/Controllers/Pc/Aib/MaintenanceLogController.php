<?php

namespace App\Http\Controllers\Pc\Aib;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use App\Models\CompanyPhone;
use App\Models\MobileDevice;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    /**
     * Mostra la dashboard del Registro Manutenzione.
     */
    public function index()
    {
        $logs = MaintenanceLog::with(['asset', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        // Raccogliamo i mezzi attualmente in stato manutenzione
        $vehiclesInMaint = Vehicle::whereIn('stato', ['manutenzione', 'alienazione'])->get();
        // Analogamente per i Dispositivi Mobili (hanno stato "Manutenzione" e "Dismesso")
        $devicesInMaint = MobileDevice::whereIn('stato', ['Manutenzione', 'Dismesso'])->get();
            
        return view('pc.aib.maintenance.index', compact('logs', 'vehiclesInMaint', 'devicesInMaint'));
    }

    /**
     * Aggiunge un evento al log e cambia lo stato dell'asset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_type' => 'required|string',
            'asset_id' => 'required|integer',
            'tipo_evento' => 'required|string',
            'note_officina' => 'nullable|string',
        ]);

        MaintenanceLog::create([
            'asset_type' => $request->asset_type,
            'asset_id' => $request->asset_id,
            'tipo_evento' => $request->tipo_evento,
            'data_evento' => now(),
            'user_id' => auth()->id(),
            'note_officina' => $request->note_officina
        ]);
        
        // Sincronizza lo stato dell'asset a seconda dell'evento
        if ($request->asset_type === Vehicle::class) {
            $v = Vehicle::find($request->asset_id);
            if ($v) {
                if (str_contains($request->tipo_evento, 'Reintegro') || str_contains($request->tipo_evento, 'Rientro')) {
                    $v->stato = 'operativo';
                } elseif (str_contains($request->tipo_evento, 'Demolizione')) {
                    $v->stato = 'alienazione';
                } else {
                    $v->stato = 'manutenzione';
                }
                $v->save();
            }
        } elseif ($request->asset_type === MobileDevice::class) {
            $m = MobileDevice::find($request->asset_id);
            if ($m) {
                if (str_contains($request->tipo_evento, 'Reintegro') || str_contains($request->tipo_evento, 'Rientro')) {
                    $m->stato = 'Attivo';
                } elseif (str_contains($request->tipo_evento, 'Demolizione')) {
                    $m->stato = 'Dismesso';
                } else {
                    $m->stato = 'Manutenzione';
                }
                $m->save();
            }
        }

        return back()->with('success', 'Stato registrato in officina con successo.');
    }
}
