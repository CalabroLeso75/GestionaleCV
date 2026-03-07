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
        $data['targa'] = strtoupper($data['targa'] ?? '');
        $data['stato'] = 'operativo'; // Forza stato operativo alla creazione

        // Clear full date if only month/anno are desired, or let it sync
        if ($request->immatricolazione_anno && $request->immatricolazione_mese) {
            $data['immatricolazione_date'] = \Carbon\Carbon::createFromDate($request->immatricolazione_anno, $request->immatricolazione_mese, 1)->toDateString();
        }

        $vehicle = new Vehicle($data);
        $data['scadenza_revisione'] = $vehicle->calculateRevisionExpiry($request->ultima_revisione);
        
        $vehicle = Vehicle::create($data);

        ActivityLogger::log('create', 'Vehicle', $vehicle->id, "Aggiunto nuovo mezzo: {$vehicle->marca} {$vehicle->modello} ({$vehicle->targa})");

        return to_route('autoparco.index')->with('success', 'Mezzo aggiunto con successo.');
    }

    public function bulkStore(Request $request)
    {
        $this->authorize('vehicle.full_edit');

        $request->validate([
            'targa_iniziale' => 'required|string|size:7',
            'targa_finale' => 'required|string|size:7',
            'marca' => 'required|string',
            'modello' => 'required|string',
            'tipo' => 'required|string',
            'immatricolazione_date' => 'nullable|date',
            'immatricolazione_mese' => 'nullable|integer|between:1,12',
            'immatricolazione_anno' => 'nullable|integer|min:1900',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'escludi_targhe' => 'nullable|string',
        ]);

        $startInfo = strtoupper(trim($request->targa_iniziale));
        $endInfo = strtoupper(trim($request->targa_finale));

        $exclude = [];
        if ($request->escludi_targhe) {
            $parts = explode(',', $request->escludi_targhe);
            foreach ($parts as $p) {
                // Se l'utente scrive solo "769", la targa intera avrà le stesse lettere iniziali e finali.
                $p = trim($p);
                if (is_numeric($p) && strlen($p) <= 3) {
                    $p = str_pad($p, 3, '0', STR_PAD_LEFT);
                    $exclude[] = substr($startInfo, 0, 2) . $p . substr($startInfo, 5, 2);
                } else {
                    $exclude[] = strtoupper($p);
                }
            }
        }

        $current = $startInfo;
        $count = 0;
        $max_iterations = 1000; // Sicurezza per evitare cicli infiniti

        $data = $request->except(['targa_iniziale', 'targa_finale', 'escludi_targhe']);
        
        if ($request->immatricolazione_anno && $request->immatricolazione_mese) {
            $data['immatricolazione_date'] = \Carbon\Carbon::createFromDate($request->immatricolazione_anno, $request->immatricolazione_mese, 1)->toDateString();
        }

        $data['km_attuali'] = 0;
        $data['stato'] = 'manca copertura assicurativa o revisione';

        while ($current !== $endInfo && $max_iterations > 0) {
            if (!in_array($current, $exclude) && !Vehicle::where('targa', $current)->exists()) {
                $data['targa'] = $current;
                Vehicle::create($data);
                $count++;
            }
            $current = $this->incrementPlate($current);
            $max_iterations--;
        }

        // Include l'ultima targa
        if (!in_array($current, $exclude) && !Vehicle::where('targa', $current)->exists() && $max_iterations > 0) {
            $data['targa'] = $current;
            Vehicle::create($data);
            $count++;
        }

        ActivityLogger::log('create', 'Vehicle', 0, "Inserimento massivo di {$count} mezzi: da {$startInfo} a {$endInfo}");

        return to_route('autoparco.index')->with('success', "Inseriti con successo {$count} mezzi.");
    }

    private function nextChar($c) {
        $valid = 'ABCDEFGHJKLMNPRSTVWXYZ';
        $pos = strpos($valid, $c);
        if ($pos !== false && $pos < strlen($valid) - 1) {
            return $valid[$pos + 1];
        }
        return 'A'; // overflow
    }

    private function incrementPlate($plate) {
        $chars = str_split($plate);
        if (count($chars) !== 7) return $plate;

        // Incrementa prima i numeri (indici 4, 3, 2)
        for ($i = 4; $i >= 2; $i--) {
            if ($chars[$i] < '9') {
                $chars[$i] = chr(ord($chars[$i]) + 1);
                return implode('', $chars);
            }
            $chars[$i] = '0';
        }

        // Poi le lettere finali (indici 6, 5)
        for ($i = 6; $i >= 5; $i--) {
            $next = $this->nextChar($chars[$i]);
            $chars[$i] = $next;
            if ($next !== 'A') return implode('', $chars);
        }

        // Poi le lettere iniziali (indici 1, 0)
        for ($i = 1; $i >= 0; $i--) {
            $next = $this->nextChar($chars[$i]);
            $chars[$i] = $next;
            if ($next !== 'A') return implode('', $chars);
        }

        return implode('', $chars);
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

        $lastRev = $vehicle->revisions()->orderBy('data_revisione', 'desc')->first();
        $minKm = $lastRev ? (int)$lastRev->km_rilevati : 0;

        $request->validate([
            'data_revisione' => 'required|date',
            'esito' => 'required|string',
            'km_rilevati' => 'required|integer|gte:' . $minKm,
            'note' => 'nullable|string',
        ], [
            'km_rilevati.gte' => "I KM rilevati inseriti non possono essere inferiori a quelli dell'ultima revisione registrata ({$minKm} km)."
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

    public function deleteRevision($id)
    {
        $this->authorize('vehicle.full_edit');

        $revision = \App\Models\VehicleRevision::findOrFail($id);
        $vehicleId = $revision->vehicle_id;
        
        ActivityLogger::log('delete', 'VehicleRevision', $revision->id, "Eliminata revisione (ID: {$revision->id}) del mezzo ID {$vehicleId}");
        
        $revision->delete();
        
        // Ricalcolo scadenza all'ultima revisione rimasta, se serve
        $vehicle = Vehicle::find($vehicleId);
        if ($vehicle) {
            $lastRev = $vehicle->revisions()->orderBy('data_revisione', 'desc')->first();
            $d = $lastRev ? $lastRev->data_revisione : null;
            $vehicle->update([
                'ultima_revisione' => $d,
                'scadenza_revisione' => $vehicle->calculateRevisionExpiry($d)
            ]);
        }

        return back()->with('success', 'Revisione eliminata con successo.');
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

        return to_route('autoparco.index')->with('success', 'Mezzo assegnato correttamente.');
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

        return to_route('autoparco.index')->with('success', "Mezzo riconsegnato. Percorsi {$diff} km.");
    }

    /**
     * Update only the insurance (assicurazione) fields of a vehicle.
     * Called via AJAX from the detail panel "Aggiorna Polizza" collapse form.
     */
    public function updateInsurance(Request $request, Vehicle $vehicle)
    {
        $this->authorize('vehicle.limited_edit');

        $request->validate([
            'assicurazione_compagnia' => 'nullable|string|max:255',
            'assicurazione_polizza'   => 'nullable|string|max:255',
            'scadenza_assicurazione'  => 'required|date',
            'assicurazione_copertura' => 'nullable|date',
        ]);

        $scadenza = $request->scadenza_assicurazione;
        $copertura = $request->assicurazione_copertura;

        // Auto-calculate copertura (+15 days) if not provided
        if (!$copertura && $scadenza) {
            $copertura = Carbon::parse($scadenza)->addDays(15)->toDateString();
        }

        $vehicle->update([
            'assicurazione_compagnia' => $request->assicurazione_compagnia,
            'assicurazione_polizza'   => $request->assicurazione_polizza,
            'scadenza_assicurazione'  => $scadenza,
            'assicurazione_copertura' => $copertura,
        ]);

        ActivityLogger::log('update', 'Vehicle', $vehicle->id, "Polizza assicurativa aggiornata per il mezzo {$vehicle->targa}");

        return response()->json(['success' => true, 'message' => 'Polizza aggiornata con successo.']);
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
        if (isset($data['targa'])) {
            $data['targa'] = strtoupper($data['targa']);
        }
        
        if ($request->immatricolazione_anno && $request->immatricolazione_mese) {
            $data['immatricolazione_date'] = \Carbon\Carbon::createFromDate($request->immatricolazione_anno, $request->immatricolazione_mese, 1)->toDateString();
        }

        // Fill data to temporary instance to calculate expiry correctly
        $vehicle->fill($data);
        $data['scadenza_revisione'] = $vehicle->calculateRevisionExpiry($request->ultima_revisione);
        
        $vehicle->update($data);

        ActivityLogger::log('update', 'Vehicle', $vehicle->id, "Dati tecnici aggiornati per il mezzo {$vehicle->targa}");

        return to_route('autoparco.index')->with('success', 'Mezzo aggiornato con successo.');
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

        $cert = OperatorCertification::create($request->all());

        ActivityLogger::log('create', 'OperatorCertification', $cert->id, "Aggiunta abilitazione: {$request->tipo}");

        return response()->json(['success' => true]);
    }

    public function deleteCertification($id)
    {
        $cert = OperatorCertification::findOrFail($id);
        ActivityLogger::log('delete', 'OperatorCertification', $id, "Rimossa abilitazione: {$cert->tipo}");
        $cert->delete();
        return response()->json(['success' => true]);
    }
}
