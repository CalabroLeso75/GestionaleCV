<?php

namespace App\Http\Controllers;

use App\Models\AibStation;
use App\Models\AibTeam;
use App\Models\AibTeamMember;
use App\Models\InternalEmployee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AibTeamController extends Controller
{
    public function index()
    {
        $teams = AibTeam::with(['stations', 'vehicles', 'phones', 'members.member'])->get();
        return view('pc.aib.teams.index', compact('teams'));
    }

    public function create()
    {
        $stations = AibStation::where('stato', 'Attivo')->get();
        $employees = InternalEmployee::where('is_aib_qualified', 1)->get(); // Personnel with AIB requirements
        $vehicles = \App\Models\Vehicle::where('stato', 'operativo')->get();
        $company_phones = \App\Models\CompanyPhone::where('stato', 'Attivo')->get();
        return view('pc.aib.teams.composition', compact('stations', 'employees', 'vehicles', 'company_phones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sigla' => 'required|string|max:255|unique:aib_teams,sigla',
            'stations' => 'required|array|min:1',
            'stations.*' => 'exists:aib_stations,id',
            'vehicles' => 'nullable|array',
            'vehicles.*' => 'exists:vehicles,id',
            'phones' => 'nullable|array',
            'phones.*' => 'exists:company_phones,id',
            'campagna' => 'nullable|string|max:255',
            'data_inizio' => 'required|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
            'turno' => 'required|in:Mattina,Pomeriggio,Notte,H24',
            'members' => 'required|array|min:1',
            'members.*.id' => 'required|exists:internal_employees,id',
            'members.*.is_caposquadra' => 'nullable|boolean',
            'members.*.is_autista' => 'nullable|boolean',
            'members.*.ruolo_specifico' => 'nullable|string',
        ]);

        $hasCaposquadra = false;
        $hasAutista = false;
        foreach ($validated['members'] as $memberData) {
            if (!empty($memberData['is_caposquadra'])) $hasCaposquadra = true;
            if (!empty($memberData['is_autista'])) $hasAutista = true;
        }

        $statoOperativo = ($hasCaposquadra && $hasAutista) ? 'Pronto' : 'Incompleta';

        $team = AibTeam::create([
            'sigla' => $validated['sigla'],
            'data_inizio' => $validated['data_inizio'],
            'data_fine' => $validated['data_fine'] ?? null,
            'campagna' => $validated['campagna'] ?? null,
            'turno' => $validated['turno'],
            'stato_operativo' => $statoOperativo,
        ]);

        $team->stations()->attach($validated['stations']);

        if (!empty($validated['vehicles'])) {
            $team->vehicles()->attach($validated['vehicles']);
        }

        if (!empty($validated['phones'])) {
            $team->phones()->attach($validated['phones']);
        }

        foreach ($validated['members'] as $memberData) {
            AibTeamMember::create([
                'team_id' => $team->id,
                'member_id' => $memberData['id'],
                'member_type' => InternalEmployee::class,
                'is_caposquadra' => $memberData['is_caposquadra'] ?? false,
                'is_autista' => $memberData['is_autista'] ?? false,
                'ruolo_specifico' => $memberData['ruolo_specifico'] ?? null,
            ]);
        }

        return redirect()->route('pc.aib.teams.index')->with('success', "Squadra {$validated['sigla']} creata con successo.");
    }
}
