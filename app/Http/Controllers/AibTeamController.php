<?php

namespace App\Http\Controllers;

use App\Models\AibStation;
use App\Models\AibTeam;
use App\Models\AibTeamMember;
use App\Models\InternalEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\ActivityLogger;

class AibTeamController extends Controller
{
    public function index()
    {
        $teams = AibTeam::with(['stations', 'vehicles', 'phones', 'members.member'])
            ->orderByRaw("CASE WHEN stato_operativo = 'Inattiva' THEN 1 ELSE 0 END")
            ->orderBy('data_inizio', 'desc')
            ->get();
        return view('pc.aib.teams.index', compact('teams'));
    }

    public function create()
    {
        $activeTeamIds = AibTeam::where('stato_operativo', '!=', 'Inattiva')
            ->where(function($q) {
                $q->whereNull('data_fine')->orWhere('data_fine', '>=', now()->toDateString());
            })->pluck('id');
            
        $assignedVehicles = \DB::table('aib_team_vehicles')->whereIn('team_id', $activeTeamIds)->pluck('vehicle_id')->toArray();
        $assignedPhones = \DB::table('aib_team_phones')->whereIn('team_id', $activeTeamIds)->pluck('phone_id')->toArray();
        $assignedDevices = \DB::table('aib_team_mobile_devices')->whereIn('team_id', $activeTeamIds)->pluck('mobile_device_id')->toArray();
        $assignedEmployees = \DB::table('aib_team_members')->whereIn('team_id', $activeTeamIds)->pluck('member_id')->toArray();

        $stations = AibStation::where('stato', 'Attivo')->get();
        $employees = InternalEmployee::where('is_aib_qualified', 1)->whereNotIn('id', $assignedEmployees)->get(); // Personnel with AIB requirements
        
        $vehicles = \App\Models\Vehicle::where('stato', 'operativo')->whereNotIn('id', $assignedVehicles)->get();
        $company_phones = \App\Models\CompanyPhone::where('stato', 'Attivo')->whereNotIn('id', $assignedPhones)->get();
        $mobile_devices = \App\Models\MobileDevice::where('stato', 'Attivo')->whereNotIn('id', $assignedDevices)->get();
        
        return view('pc.aib.teams.composition', compact('stations', 'employees', 'vehicles', 'company_phones', 'mobile_devices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sigla' => 'required|string|max:255|unique:aib_teams,sigla',
            'stations' => 'required|array|min:1',
            'stations.*' => 'exists:aib_stations,id',
            'vehicles' => 'required|array|min:1',
            'vehicles.*' => 'exists:vehicles,id',
            'phones' => 'nullable|array',
            'phones.*' => 'exists:company_phones,id',
            'mobile_devices' => 'nullable|array',
            'mobile_devices.*' => 'exists:mobile_devices,id',
            'campagna' => 'nullable|string|max:255',
            'data_inizio' => 'required|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
            'members' => 'required|array|min:1',
            'members.*.id' => 'required|exists:internal_employees,id',
            'members.*.is_caposquadra' => 'nullable|boolean',
            'members.*.is_autista' => 'nullable|boolean',
            'members.*.ruolo_specifico' => 'nullable|string',
            'note' => 'nullable|string'
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
            'stato_operativo' => $statoOperativo,
            'note' => $validated['note'] ?? null,
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

        if (!empty($validated['mobile_devices'])) {
            $team->mobileDevices()->attach($validated['mobile_devices']);
        }

        // --- MOTORE DI REGISTRAZIONE LOG ASSEGNAZIONI (INIZIALE) ---
        // Calcolo capisquadra
        $capisquadraNames = [];
        $team->load('members.member'); // ricarichiamo coi real members
        foreach($team->members as $teamMember) {
            if ($teamMember->is_caposquadra && $teamMember->member) {
                // Presume member has first_name and last_name
                $capisquadraNames[] = $teamMember->member->first_name . ' ' . $teamMember->member->last_name;
            }
        }

        $userId = auth()->id();

        // Log vehicles
        if ($team->vehicles) {
            foreach ($team->vehicles as $vehicle) {
                \App\Models\TeamAssetLog::create([
                    'team_id' => $team->id,
                    'asset_type' => 'Veicolo',
                    'asset_name' => $vehicle->targa . ' - ' . $vehicle->modello,
                    'action' => 'Consegna Iniziale',
                    'old_assignees' => null,
                    'new_assignees' => $capisquadraNames,
                    'user_id' => $userId
                ]);
            }
        }

        // Log SIM phones
        if ($team->phones) {
            foreach ($team->phones as $phone) {
                \App\Models\TeamAssetLog::create([
                    'team_id' => $team->id,
                    'asset_type' => 'Telefono con SIM',
                    'asset_name' => $phone->numero . ($phone->alias ? " ({$phone->alias})" : ""),
                    'action' => 'Consegna Iniziale',
                    'old_assignees' => null,
                    'new_assignees' => $capisquadraNames,
                    'user_id' => $userId
                ]);
            }
        }

        // Log Mobile Devices
        if ($team->mobileDevices) {
            foreach ($team->mobileDevices as $device) {
                \App\Models\TeamAssetLog::create([
                    'team_id' => $team->id,
                    'asset_type' => 'Dispositivo Mobile',
                    'asset_name' => $device->marca . ' ' . $device->modello . ' (' . ($device->seriale ?? $device->imei) . ')',
                    'action' => 'Consegna Iniziale',
                    'old_assignees' => null,
                    'new_assignees' => $capisquadraNames,
                    'user_id' => $userId
                ]);
            }
        }
        
        ActivityLogger::log('create', 'AibTeam', $team->id, "Squadra AIB (Sigla: {$team->sigla}) configurata. Stato: {$statoOperativo}");

        return to_route('pc.aib.teams.index')->with('success', "Squadra {$validated['sigla']} creata con successo.");
    }

    public function edit(AibTeam $team)
    {
        $team->load(['stations', 'members.member', 'vehicles', 'phones', 'mobileDevices']);
        
        $activeTeamIds = AibTeam::where('id', '!=', $team->id)
            ->where('stato_operativo', '!=', 'Inattiva')
            ->where(function($q) {
                $q->whereNull('data_fine')->orWhere('data_fine', '>=', now()->toDateString());
            })->pluck('id');
            
        $assignedVehicles = \DB::table('aib_team_vehicles')->whereIn('team_id', $activeTeamIds)->pluck('vehicle_id')->toArray();
        $assignedPhones = \DB::table('aib_team_phones')->whereIn('team_id', $activeTeamIds)->pluck('phone_id')->toArray();
        $assignedDevices = \DB::table('aib_team_mobile_devices')->whereIn('team_id', $activeTeamIds)->pluck('mobile_device_id')->toArray();
        $assignedEmployees = \DB::table('aib_team_members')->whereIn('team_id', $activeTeamIds)->pluck('member_id')->toArray();

        $stations = AibStation::where('stato', 'Attivo')->get();
        $employees = InternalEmployee::where('is_aib_qualified', 1)->whereNotIn('id', $assignedEmployees)->get();
        
        $vehicles = \App\Models\Vehicle::where('stato', 'operativo')->whereNotIn('id', $assignedVehicles)->get();
        $company_phones = \App\Models\CompanyPhone::where('stato', 'Attivo')->whereNotIn('id', $assignedPhones)->get();
        $mobile_devices = \App\Models\MobileDevice::where('stato', 'Attivo')->whereNotIn('id', $assignedDevices)->get();
        
        return view('pc.aib.teams.edit', compact('team', 'stations', 'employees', 'vehicles', 'company_phones', 'mobile_devices'));
    }

    public function update(Request $request, AibTeam $team)
    {
        $validated = $request->validate([
            'sigla' => 'required|string|max:255',
            'stations' => 'required|array|min:1',
            'stations.*' => 'exists:aib_stations,id',
            'members' => 'required|array|min:1',
            'members.*.id' => 'exists:internal_employees,id',
            'vehicles' => 'required|array|min:1',
            'vehicles.*' => 'exists:vehicles,id',
            'phones' => 'nullable|array',
            'phones.*' => 'exists:company_phones,id',
            'mobile_devices' => 'nullable|array',
            'mobile_devices.*' => 'exists:mobile_devices,id',
            'campagna' => 'nullable|string|max:255',
            'data_inizio' => 'required|date',
            'data_fine' => 'nullable|date|after_or_equal:data_inizio',
            'note' => 'nullable|string'
        ]);

        $team->load(['members.member', 'vehicles', 'phones', 'mobileDevices']);

        // Estrazione VECCHI capisquadra
        $oldCapisquadraNames = [];
        foreach($team->members as $teamMember) {
            if ($teamMember->is_caposquadra && $teamMember->member) {
                $oldCapisquadraNames[] = $teamMember->member->first_name . ' ' . $teamMember->member->last_name;
            }
        }
        sort($oldCapisquadraNames); // per confrontarli array == array
        
        // Risorse attuali (ID correnti)
        $oldVehicles = $team->vehicles->pluck('id')->toArray();
        $oldPhones = $team->phones->pluck('id')->toArray();
        $oldDevices = $team->mobileDevices->pluck('id')->toArray();

        // 1. Aggiornamento base team
        $wasOperativo = $team->data_fine === null;
        $team->update([
            'sigla' => strtoupper($validated['sigla']),
            'campagna' => $validated['campagna'] ?? null,
            'data_inizio' => $validated['data_inizio'],
            'data_fine' => $validated['data_fine'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        // 2. Sincronizzazione Postazioni
        $team->stations()->sync($validated['stations']);

        // 3. Sincronizzazione Membri
        $team->members()->delete();
        $capisquadraNames = [];
        foreach ($validated['members'] as $memberData) {
            $isCapo = !empty($memberData['is_caposquadra']);
            AibTeamMember::create([
                'team_id' => $team->id,
                'member_id' => $memberData['id'],
                'member_type' => InternalEmployee::class,
                'is_caposquadra' => $isCapo,
                'is_autista' => !empty($memberData['is_autista']),
                'ruolo_specifico' => $memberData['ruolo_specifico'] ?? null,
            ]);
            
            if ($isCapo) {
                $emp = \App\Models\InternalEmployee::find($memberData['id']);
                if ($emp) {
                    $capisquadraNames[] = $emp->first_name . ' ' . $emp->last_name;
                }
            }
        }
        sort($capisquadraNames);
        
        $capisquadraChanged = ($oldCapisquadraNames !== $capisquadraNames);

        // 4. Sincronizzazione Risorse
        $newVehicles = $validated['vehicles'] ?? [];
        $newPhones = $validated['phones'] ?? [];
        $newDevices = $validated['mobile_devices'] ?? [];

        $team->vehicles()->sync($newVehicles);
        $team->phones()->sync($newPhones);
        $team->mobileDevices()->sync($newDevices);

        // 5. REGISTRAZIONE STORICO LOG
        $userId = auth()->id();

        // Helpers engine log
        $processAssetsLog = function($repoModel, string $assetType, array $oldIds, array $newIds) use ($team, $oldCapisquadraNames, $capisquadraNames, $capisquadraChanged, $userId) {
            $added = array_diff($newIds, $oldIds);
            $removed = array_diff($oldIds, $newIds);
            $kept = array_intersect($oldIds, $newIds);

            // Added => Consegna Iniziale ai nuovi
            foreach ($added as $id) {
                $asset = $repoModel::find($id);
                if (!$asset) continue;
                $name = $assetType == 'Veicolo' ? ($asset->targa . ' - ' . $asset->modello) :
                       ($assetType == 'Telefono con SIM' ? ($asset->numero . ($asset->alias ? " ({$asset->alias})" : "")) :
                       ($asset->marca . ' ' . $asset->modello . ' (' . ($asset->seriale ?? $asset->imei) . ')'));
                
                \App\Models\TeamAssetLog::create([
                    'team_id' => $team->id, 'asset_type' => $assetType, 'asset_name' => $name,
                    'action' => 'Consegna Iniziale', 'old_assignees' => null, 'new_assignees' => $capisquadraNames, 'user_id' => $userId
                ]);
            }
            // Removed => Ritiro dai vecchi
            foreach ($removed as $id) {
                $asset = $repoModel::find($id);
                if (!$asset) continue;
                $name = $assetType == 'Veicolo' ? ($asset->targa . ' - ' . $asset->modello) :
                       ($assetType == 'Telefono con SIM' ? ($asset->numero . ($asset->alias ? " ({$asset->alias})" : "")) :
                       ($asset->marca . ' ' . $asset->modello . ' (' . ($asset->seriale ?? $asset->imei) . ')'));
                
                \App\Models\TeamAssetLog::create([
                    'team_id' => $team->id, 'asset_type' => $assetType, 'asset_name' => $name,
                    'action' => 'Ritiro', 'old_assignees' => $oldCapisquadraNames, 'new_assignees' => null, 'user_id' => $userId
                ]);
            }
            // Kept => Cambio se capisquadra sono diversi
            if ($capisquadraChanged) {
                foreach ($kept as $id) {
                    $asset = $repoModel::find($id);
                    if (!$asset) continue;
                    $name = $assetType == 'Veicolo' ? ($asset->targa . ' - ' . $asset->modello) :
                           ($assetType == 'Telefono con SIM' ? ($asset->numero . ($asset->alias ? " ({$asset->alias})" : "")) :
                           ($asset->marca . ' ' . $asset->modello . ' (' . ($asset->seriale ?? $asset->imei) . ')'));
                    
                    \App\Models\TeamAssetLog::create([
                        'team_id' => $team->id, 'asset_type' => $assetType, 'asset_name' => $name,
                        'action' => 'Cambio Assegnatari', 'old_assignees' => $oldCapisquadraNames, 'new_assignees' => $capisquadraNames, 'user_id' => $userId
                    ]);
                }
            }
        };

        $processAssetsLog(\App\Models\Vehicle::class, 'Veicolo', $oldVehicles, $newVehicles);
        $processAssetsLog(\App\Models\CompanyPhone::class, 'Telefono con SIM', $oldPhones, $newPhones);
        $processAssetsLog(\App\Models\MobileDevice::class, 'Dispositivo Mobile', $oldDevices, $newDevices);

        ActivityLogger::log('update', 'AibTeam', $team->id, "Parametri Squadra AIB (Sigla: {$team->sigla}) modificati. " . ($capisquadraChanged ? 'Capisquadra aggiornati.' : ''));

        return to_route('pc.aib.teams.index')->with('success', "Squadra {$validated['sigla']} aggiornata con successo.");
    }

    public function destroy(AibTeam $team)
    {
        if ($team->stato_operativo === 'Inattiva') {
            return back()->with('error', 'La squadra è già inattiva.');
        }

        $team->load(['members.member', 'vehicles', 'phones', 'mobileDevices']);

        // Estrazione VECCHI capisquadra
        $oldCapisquadraNames = [];
        foreach($team->members as $teamMember) {
            if ($teamMember->is_caposquadra && $teamMember->member) {
                $oldCapisquadraNames[] = $teamMember->member->first_name . ' ' . $teamMember->member->last_name;
            }
        }
        sort($oldCapisquadraNames);

        $userId = auth()->id();

        // Helpers engine log
        $processWithdrawalLog = function($repoModel, string $assetType, $items) use ($team, $oldCapisquadraNames, $userId) {
            foreach ($items as $item) {
                $name = $assetType == 'Veicolo' ? ($item->targa . ' - ' . $item->modello) :
                       ($assetType == 'Telefono con SIM' ? ($item->numero . ($item->alias ? " ({$item->alias})" : "")) :
                       ($item->marca . ' ' . $item->modello . ' (' . ($item->seriale ?? $item->imei) . ')'));
                
                \App\Models\TeamAssetLog::create([
                    'team_id' => $team->id, 'asset_type' => $assetType, 'asset_name' => $name,
                    'action' => 'Ritiro (Chiusura Squadra)', 'old_assignees' => $oldCapisquadraNames, 'new_assignees' => null, 'user_id' => $userId
                ]);
            }
        };

        $processWithdrawalLog(\App\Models\Vehicle::class, 'Veicolo', $team->vehicles);
        $processWithdrawalLog(\App\Models\CompanyPhone::class, 'Telefono con SIM', $team->phones);
        $processWithdrawalLog(\App\Models\MobileDevice::class, 'Dispositivo Mobile', $team->mobileDevices);

        $team->update([
            'stato_operativo' => 'Inattiva',
            'data_fine' => now()->toDateString(),
        ]);

        ActivityLogger::log('delete', 'AibTeam', $team->id, "Squadra AIB (Sigla: {$team->sigla}) chiusa/annullata e passata tra le inattive.");

        return to_route('pc.aib.teams.index')->with('success', "Squadra {$team->sigla} ritirata e resa inattiva con successo.");
    }
}
