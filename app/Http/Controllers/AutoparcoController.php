<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleLog;
use App\Models\User;
use App\Models\VehicleRevision;
use App\Models\VehicleType;
use App\Models\OperatorCertification;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AutoparcoController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('currentLog.user')->get();
        $users = User::with('certifications')->orderBy('surname')->get();

        // Calculate expirations
        $expiringAssicurazione = Vehicle::where('scadenza_assicurazione', '<=', Carbon::now()->addDays(30))
            ->where('stato', '!=', 'fuori servizio')
            ->get();
        
        $expiringRevisione = Vehicle::where('scadenza_revisione', '<=', Carbon::now()->addDays(30))
            ->where('stato', '!=', 'fuori servizio')
            ->get();
        
        $vehicleTypes = \App\Models\VehicleType::orderBy('name')->get();

        return view('autoparco.index', compact('vehicles', 'users', 'expiringAssicurazione', 'expiringRevisione', 'vehicleTypes'));
    }

    public function getLogs(Vehicle $vehicle)
    {
        $this->authorize('vehicle.view_logs');

        $logs = ActivityLog::with('user')
            ->where('model', 'Vehicle')
            ->where('model_id', $vehicle->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $this->authorize('vehicle.full_edit');

        $request->validate([
            'targa' => 'required|string|unique:vehicles,targa',
            'marca' => 'required|string',
            'modello' => 'required|string',
            'tipo' => 'required|string',
            'km_attuali' => 'required|integer|min:0',
            'immatricolazione_date' => 'nullable|date',
            'immatricolazione_mese' => 'nullable|integer|between:1,12',
            'immatricolazione_anno' => 'nullable|integer|min:1900',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'assicurazione_compagnia' => 'nullable|string',
            'assicurazione_polizza' => 'nullable|string',
            'scadenza_assicurazione' => 'nullable|date',
            'assicurazione_copertura' => 'nullable|date',
            'ultima_revisione' => 'nullable|date',
        ]);

        $data = $request->all();
        // Clear full date if only month/anno are desired, or let it sync
        if ($request->immatricolazione_anno && $request->immatricolazione_mese) {
            $data['immatricolazione_date'] = \Carbon\Carbon::createFromDate($request->immatricolazione_anno, $request->immatricolazione_mese, 1)->toDateString();
        }

        $vehicle = new Vehicle($data);
        $data['scadenza_revisione'] = $vehicle->calculateRevisionExpiry($request->ultima_revisione);
        
        $vehicle = Vehicle::create($data);

        ActivityLogger::log('create', 'Vehicle', $vehicle->id, "Aggiunto nuovo mezzo: {$vehicle->marca} {$vehicle->modello} ({$vehicle->targa})");

        return redirect()->route('autoparco.index')->with('success', 'Mezzo aggiunto con successo.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['revisions' => function($q) {
            $q->orderBy('data_revisione', 'desc');
        }, 'logs.user', 'vehicleType']);
        
        return response()->json($vehicle);
    }

    public function addRevision(Request $request, Vehicle $vehicle)
    {
        $this->authorize('vehicle.limited_edit');

        $request->validate([
            'data_revisione' => 'required|date',
            'esito' => 'required|string',
            'km_rilevati' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);

        $vehicle->revisions()->create($request->all());
        
        // Update vehicle expiry
        $vehicle->update([
            'ultima_revisione' => $request->data_revisione,
            'scadenza_revisione' => $vehicle->calculateRevisionExpiry($request->data_revisione)
        ]);

        ActivityLogger::log('update', 'Vehicle', $vehicle->id, "Aggiunta revisione per il mezzo {$vehicle->targa}");

        return back()->with('success', 'Revisione aggiunta con successo.');
    }

    public function updateStatus(Request $request, Vehicle $vehicle)
    {
        $this->authorize('vehicle.limited_edit');

        $request->validate(['stato' => 'required|string']);
        $vehicle->update(['stato' => $request->stato]);

        ActivityLogger::log('update', 'Vehicle', $vehicle->id, "Aggiornato stato a: {$request->stato}");

        return back()->with('success', 'Stato aggiornato con successo.');
    }

    public function assign(Request $request, Vehicle $vehicle)
    {
        $this->authorize('vehicle.assign');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'km_iniziali' => 'required|integer|min:'.$vehicle->km_attuali,
            'note' => 'nullable|string',
        ]);

        // Check if already assigned
        if ($vehicle->currentLog) {
            return back()->with('error', 'Il mezzo è già assegnato.');
        }

        // Habilitation Check
        if ($vehicle->vehicleType && $vehicle->vehicleType->patente) {
            $user = User::findOrFail($request->user_id);
            $hasValidCert = OperatorCertification::where('user_id', $user->id)
                ->where('tipo', 'LIKE', '%' . $vehicle->vehicleType->patente . '%')
                ->where('scadenza', '>', now())
                ->exists();

            if (!$hasValidCert) {
                return back()->with('error', "L'operatore non possiede l'abilitazione richiesta ({$vehicle->vehicleType->patente}) o la stessa è scaduta.");
            }
        }

        $log = VehicleLog::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $request->user_id,
            'km_iniziali' => $request->km_iniziali,
            'assegnato_il' => now(),
            'note' => $request->note,
        ]);

        $vehicle->update(['stato' => 'in uso', 'km_attuali' => $request->km_iniziali]);

        ActivityLogger::log('assign', 'Vehicle', $vehicle->id, "Mezzo {$vehicle->targa} assegnato a " . User::find($request->user_id)->name);

        return redirect()->route('autoparco.index')->with('success', 'Mezzo assegnato correttamente.');
    }

    public function returnVehicle(Request $request, Vehicle $vehicle)
    {
        $this->authorize('vehicle.assign');

        $log = $vehicle->currentLog;
        if (!$log) {
            return back()->with('error', 'Nessuna assegnazione attiva trovata per questo mezzo.');
        }

        $request->validate([
            'km_finali' => 'required|integer|min:'.$log->km_iniziali,
            'note' => 'nullable|string',
        ]);

        $diff = $request->km_finali - $log->km_iniziali;

        $log->update([
            'km_finali' => $request->km_finali,
            'riconsegnato_il' => now(),
            'note' => ($log->note ? $log->note . "\n" : "") . "Riconsegna: " . $request->note . " (Percorsi: {$diff} km)",
        ]);

        $vehicle->update([
            'stato' => 'operativo',
            'km_attuali' => $request->km_finali
        ]);

        ActivityLogger::log('return', 'Vehicle', $vehicle->id, "Mezzo {$vehicle->targa} riconsegnato. Percorsi {$diff} km.");

        return redirect()->route('autoparco.index')->with('success', "Mezzo riconsegnato. Percorsi {$diff} km.");
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorize('vehicle.full_edit');

        $request->validate([
            'targa' => 'required|string|unique:vehicles,targa,'.$vehicle->id,
            'marca' => 'required|string',
            'modello' => 'required|string',
            'tipo' => 'required|string',
            'km_attuali' => 'required|integer|min:0',
            'immatricolazione_date' => 'nullable|date',
            'immatricolazione_mese' => 'nullable|integer|between:1,12',
            'immatricolazione_anno' => 'nullable|integer|min:1900',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'assicurazione_compagnia' => 'nullable|string',
            'assicurazione_polizza' => 'nullable|string',
            'scadenza_assicurazione' => 'nullable|date',
            'assicurazione_copertura' => 'nullable|date',
            'ultima_revisione' => 'nullable|date',
        ]);

        $data = $request->all();
        if ($request->immatricolazione_anno && $request->immatricolazione_mese) {
            $data['immatricolazione_date'] = \Carbon\Carbon::createFromDate($request->immatricolazione_anno, $request->immatricolazione_mese, 1)->toDateString();
        }

        // Fill data to temporary instance to calculate expiry correctly
        $vehicle->fill($data);
        $data['scadenza_revisione'] = $vehicle->calculateRevisionExpiry($request->ultima_revisione);
        
        $vehicle->update($data);

        ActivityLogger::log('update', 'Vehicle', $vehicle->id, "Dati tecnici aggiornati per il mezzo {$vehicle->targa}");

        return redirect()->route('autoparco.index')->with('success', 'Mezzo aggiornato con successo.');
    }
    public function getCertifications($userId)
    {
        return response()->json(OperatorCertification::where('user_id', $userId)->get());
    }

    public function storeCertification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|string',
            'scadenza' => 'required|date',
            'documento' => 'nullable|string',
        ]);

        OperatorCertification::create($request->all());

        return response()->json(['success' => true]);
    }

    public function deleteCertification($id)
    {
        OperatorCertification::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
