<?php

namespace App\Http\Controllers;

use App\Models\CompanyPhone;
use Illuminate\Http\Request;

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

        CompanyPhone::create($validated);

        return redirect()->route('pc.aib.phones.index')->with('success', 'Telefono/SIM creato con successo.');
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

        return redirect()->route('pc.aib.phones.index')->with('success', 'Telefono/SIM aggiornato con successo.');
    }

    public function destroy(CompanyPhone $phone)
    {
        $phone->delete();
        return redirect()->route('pc.aib.phones.index')->with('success', 'Telefono/SIM eliminato con successo.');
    }
}
