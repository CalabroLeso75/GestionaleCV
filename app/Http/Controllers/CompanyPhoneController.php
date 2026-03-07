<?php

namespace App\Http\Controllers;

use App\Models\CompanyPhone;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class CompanyPhoneController extends Controller
{
    public function index()
    {
        $phones = CompanyPhone::all();
        return view('pc.aib.phones.index', compact('phones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:255|unique:company_phones,numero',
            'alias' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'operatore' => 'nullable|string|max:255',
            'piano_telefonico' => 'nullable|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo'
        ]);

        $phone = CompanyPhone::create($validated);
        
        ActivityLogger::log('create', 'CompanyPhone', $phone->id, "Aggiunto telefono/SIM: {$phone->numero}");

        return to_route('pc.aib.phones.index')->with('success', 'Telefono/SIM creato con successo.');
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'numero_iniziale' => 'required|string|max:255',
            'quantita' => 'required|integer|min:1|max:100',
            'alias' => 'nullable|string|max:255',
            'operatore' => 'nullable|string|max:255',
            'piano_telefonico' => 'nullable|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo'
        ]);

        $startNumber = $validated['numero_iniziale'];
        $quantity = $validated['quantita'];
        $createdCount = 0;

        // Try to extract a numeric suffix to increment
        preg_match('/^(.*?)(\d+)$/', $startNumber, $matches);
        
        if (count($matches) == 3) {
            $prefix = $matches[1];
            $numericPart = $matches[2];
            $length = strlen($numericPart);
            $currentVal = (int)$numericPart;

            for ($i = 0; $i < $quantity; $i++) {
                $newNumber = $prefix . str_pad($currentVal, $length, '0', STR_PAD_LEFT);
                
                // Only create if number doesn't exist
                if (!CompanyPhone::where('numero', $newNumber)->exists()) {
                    CompanyPhone::create([
                        'numero' => $newNumber,
                        'alias' => $validated['alias'] ?? null,
                        'operatore' => $validated['operatore'] ?? null,
                        'piano_telefonico' => $validated['piano_telefonico'] ?? null,
                        'stato' => $validated['stato']
                    ]);
                    $createdCount++;
                }
                
                $currentVal++;
            }
        } else {
             // If not ending in numbers, just create the one (or error out, but we'll try one)
             if (!CompanyPhone::where('numero', $startNumber)->exists()) {
                 CompanyPhone::create([
                     'numero' => $startNumber,
                     'alias' => $validated['alias'] ?? null,
                     'operatore' => $validated['operatore'] ?? null,
                     'piano_telefonico' => $validated['piano_telefonico'] ?? null,
                     'stato' => $validated['stato']
                 ]);
                 $createdCount++;
             }
        }

        ActivityLogger::log('create', 'CompanyPhone', 0, "Creati {$createdCount} numeri a partire da {$startNumber}");

        return to_route('pc.aib.phones.index')->with('success', "Creati {$createdCount} numeri telefonici con successo.");
    }

    public function update(Request $request, CompanyPhone $phone)
    {
        $validated = $request->validate([
            'numero' => "required|string|max:255|unique:company_phones,numero,{$phone->id}",
            'alias' => 'nullable|string|max:255',
            'imei' => 'nullable|string|max:255',
            'operatore' => 'nullable|string|max:255',
            'piano_telefonico' => 'nullable|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo'
        ]);

        $phone->update($validated);
        
        ActivityLogger::log('update', 'CompanyPhone', $phone->id, "Aggiornato telefono/SIM: {$phone->numero}");

        return to_route('pc.aib.phones.index')->with('success', 'Telefono/SIM aggiornato con successo.');
    }

    public function destroy(CompanyPhone $phone)
    {
        ActivityLogger::log('delete', 'CompanyPhone', $phone->id, "Eliminato telefono/SIM: {$phone->numero}");
        $phone->delete();
        return to_route('pc.aib.phones.index')->with('success', 'Telefono/SIM eliminato con successo.');
    }
}
