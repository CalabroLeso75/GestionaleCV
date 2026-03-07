<?php

namespace App\Http\Controllers\Pc\Aib;

use App\Http\Controllers\Controller;
use App\Models\ResourceAssignment;
use App\Models\ResourceAssignmentLog;
use App\Models\Vehicle;
use App\Models\CompanyPhone;
use App\Models\MobileDevice;
use App\Models\InternalEmployee;
use App\Models\ExternalEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourceAssignmentController extends Controller
{
    /**
     * Mostra lo storico universale di tutte le assegnazioni.
     */
    public function index()
    {
        $logs = ResourceAssignmentLog::with(['assignable', 'assignee', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        return view('pc.aib.resource_assignments.index', compact('logs'));
    }

    /**
     * Elabora una nuova assegnazione per un asset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'assignable_type' => 'required|string',
            'assignable_id' => 'required|integer',
            'assignee_type' => 'required|string',
            'assignee_id' => 'required|integer',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Chiudi eventuali assegnazioni attive per questa risorsa
            $activeAssignment = ResourceAssignment::where('assignable_type', $request->assignable_type)
                ->where('assignable_id', $request->assignable_id)
                ->whereNull('data_restituzione')
                ->first();

            if ($activeAssignment) {
                $activeAssignment->update([
                    'data_restituzione' => now(),
                    'note_restituzione' => 'Restituzione automatica per nuova assegnazione.'
                ]);
                
                ResourceAssignmentLog::create([
                    'resource_assignment_id' => $activeAssignment->id,
                    'assignable_type' => $activeAssignment->assignable_type,
                    'assignable_id' => $activeAssignment->assignable_id,
                    'assignee_type' => $activeAssignment->assignee_type,
                    'assignee_id' => $activeAssignment->assignee_id,
                    'azione' => 'Restituzione (Automatica)',
                    'user_id' => auth()->id(),
                    'note' => 'Restituzione forzata a causa di una sub-assegnazione.'
                ]);
            }

            // Crea la nuova assegnazione
            $assignment = ResourceAssignment::create([
                'assignable_type' => $request->assignable_type,
                'assignable_id' => $request->assignable_id,
                'assignee_type' => $request->assignee_type,
                'assignee_id' => $request->assignee_id,
                'data_assegnazione' => now(),
                'note_assegnazione' => $request->note,
            ]);

            // Traccia il Log
            ResourceAssignmentLog::create([
                'resource_assignment_id' => $assignment->id,
                'assignable_type' => $assignment->assignable_type,
                'assignable_id' => $assignment->assignable_id,
                'assignee_type' => $assignment->assignee_type,
                'assignee_id' => $assignment->assignee_id,
                'azione' => 'Assegnazione Diretta',
                'user_id' => auth()->id(),
                'note' => $request->note
            ]);
            
            // Aggiorna lo stato della risorsa madre per renderlo palese se necessario
            $this->updateAssetStatus($request->assignable_type, $request->assignable_id, 'in uso');

            DB::commit();
            return back()->with('success', 'Risorsa assegnata con successo.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Errore durante l\'assegnazione: ' . $e->getMessage());
        }
    }

    /**
     * Chiude (Restituisce) un'assegnazione attiva.
     */
    public function return(Request $request, $id)
    {
        $assignment = ResourceAssignment::findOrFail($id);

        DB::beginTransaction();
        try {
            $assignment->update([
                'data_restituzione' => now(),
                'note_restituzione' => $request->note
            ]);

            ResourceAssignmentLog::create([
                'resource_assignment_id' => $assignment->id,
                'assignable_type' => $assignment->assignable_type,
                'assignable_id' => $assignment->assignable_id,
                'assignee_type' => $assignment->assignee_type,
                'assignee_id' => $assignment->assignee_id,
                'azione' => 'Restituzione',
                'user_id' => auth()->id(),
                'note' => $request->note
            ]);
            
            $this->updateAssetStatus($assignment->assignable_type, $assignment->assignable_id, 'operativo');

            DB::commit();
            return back()->with('success', 'Risorsa ritirata con successo.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Errore durante il ritiro: ' . $e->getMessage());
        }
    }
    
    // Metodo helper per cambiare lo stato ad un asset se dispone di questa nozione
    private function updateAssetStatus($type, $id, $status)
    {
        if ($type === Vehicle::class) {
            Vehicle::where('id', $id)->update(['stato' => $status]);
        }
        // I telefoni e mobile_devices al momento usano ("Attivo" / "Inattivo") non "in uso", quindi li lasciamo Attivi
    }
}
