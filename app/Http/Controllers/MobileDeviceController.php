<?php

namespace App\Http\Controllers;

use App\Models\MobileDevice;
use Illuminate\Http\Request;

use App\Models\InternalEmployee;

class MobileDeviceController extends Controller
{
    public function index()
    {
        $devices = MobileDevice::with(['activeAssignment.assignee'])
                                ->orderBy('marca')->orderBy('modello')
                                ->get();
        $employees = InternalEmployee::orderBy('last_name')->get();
        return view('pc.aib.mobile_devices.index', compact('devices', 'employees'));
    }

    public function create()
    {
        return view('pc.aib.mobile_devices.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'marca'                => 'required|string|max:255',
            'modello'              => 'required|string|max:255',
            'tipo'                 => 'nullable|in:smartphone,tablet,altro',
            'colore'               => 'nullable|string|max:100',
            'anno_acquisto'        => 'nullable|integer|min:2000|max:2099',
            'asset_code'           => 'nullable|string|max:100|unique:mobile_devices,asset_code',
            'numero_telefono'      => 'nullable|string|max:50',
            'imei'                 => 'nullable|string|max:255|unique:mobile_devices,imei',
            'seriale'              => 'nullable|string|max:255|unique:mobile_devices,seriale',
            'stato'                => 'required|in:Attivo,Inattivo,Manutenzione,Dismesso',
            'sistema_operativo'    => 'nullable|string|max:100',
            'versione_os'          => 'nullable|string|max:50',
            'dimensione_schermo'   => 'nullable|numeric|min:3|max:15',
            'memoria_ram'          => 'nullable|string|max:20',
            'memoria_storage'      => 'nullable|string|max:20',
            'processore'           => 'nullable|string|max:255',
            'fotocamera_principale'=> 'nullable|string|max:255',
            '5g'                   => 'nullable|boolean',
            'nfc'                  => 'nullable|boolean',
            'batteria_mah'         => 'nullable|string|max:50',
            'note'                 => 'nullable|string',
        ]);

        $validated['5g']  = $request->has('5g')  ? 1 : 0;
        $validated['nfc'] = $request->has('nfc') ? 1 : 0;

        MobileDevice::create($validated);

        return to_route('pc.aib.mobile_devices.index')->with('success', 'Dispositivo mobile aggiunto con successo.');
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'imei_iniziale' => 'nullable|string|max:255',
            'quantita' => 'required|integer|min:1|max:100',
            'marca' => 'required|string|max:255',
            'modello' => 'required|string|max:255',
            'stato' => 'required|in:Attivo,Inattivo,Manutenzione,Dismesso',
            'note' => 'nullable|string'
        ]);

        $quantita = $validated['quantita'];
        $imei_iniziale = $validated['imei_iniziale'] ?? null;
        
        $count = 0;
        for ($i = 0; $i < $quantita; $i++) {
            $currentImei = $imei_iniziale;
            
            if ($imei_iniziale) {
                // Increment logic
                if (preg_match('/^(.*?)(\d+)$/', $imei_iniziale, $matches)) {
                    $prefix = $matches[1];
                    $number = $matches[2];
                    $length = strlen($number);
                    $newNumber = str_pad((int)$number + $i, $length, '0', STR_PAD_LEFT);
                    $currentImei = $prefix . $newNumber;
                } else {
                    if ($i > 0) {
                        $currentImei = $imei_iniziale . '-' . $i; // Fallback se non ci sono numeri in fondo
                    }
                }
            }

            // Evita duplicati
            if ($currentImei && MobileDevice::where('imei', $currentImei)->exists()) {
                continue;
            }

            MobileDevice::create([
                'marca' => $validated['marca'],
                'modello' => $validated['modello'],
                'imei' => $currentImei,
                'stato' => $validated['stato'],
                'note' => $validated['note']
            ]);
            $count++;
        }

        return to_route('pc.aib.mobile_devices.index')->with('success', "$count dispositivi generati con successo in blocco.");
    }

    public function edit(MobileDevice $mobileDevice)
    {
        return view('pc.aib.mobile_devices.form', ['device' => $mobileDevice]);
    }

    public function update(Request $request, MobileDevice $mobileDevice)
    {
        $validated = $request->validate([
            'marca'                => 'required|string|max:255',
            'modello'              => 'required|string|max:255',
            'tipo'                 => 'nullable|in:smartphone,tablet,altro',
            'colore'               => 'nullable|string|max:100',
            'anno_acquisto'        => 'nullable|integer|min:2000|max:2099',
            'asset_code'           => 'nullable|string|max:100|unique:mobile_devices,asset_code,' . $mobileDevice->id,
            'numero_telefono'      => 'nullable|string|max:50',
            'imei'                 => 'nullable|string|max:255|unique:mobile_devices,imei,' . $mobileDevice->id,
            'seriale'              => 'nullable|string|max:255|unique:mobile_devices,seriale,' . $mobileDevice->id,
            'stato'                => 'required|in:Attivo,Inattivo,Manutenzione,Dismesso',
            'sistema_operativo'    => 'nullable|string|max:100',
            'versione_os'          => 'nullable|string|max:50',
            'dimensione_schermo'   => 'nullable|numeric|min:3|max:15',
            'memoria_ram'          => 'nullable|string|max:20',
            'memoria_storage'      => 'nullable|string|max:20',
            'processore'           => 'nullable|string|max:255',
            'fotocamera_principale'=> 'nullable|string|max:255',
            '5g'                   => 'nullable|boolean',
            'nfc'                  => 'nullable|boolean',
            'batteria_mah'         => 'nullable|string|max:50',
            'note'                 => 'nullable|string',
        ]);

        $validated['5g']  = $request->has('5g')  ? 1 : 0;
        $validated['nfc'] = $request->has('nfc') ? 1 : 0;

        $mobileDevice->update($validated);

        return to_route('pc.aib.mobile_devices.index')->with('success', 'Dispositivo mobile aggiornato con successo.');
    }

    public function destroy(MobileDevice $mobileDevice)
    {
        $mobileDevice->delete();
        return to_route('pc.aib.mobile_devices.index')->with('success', 'Dispositivo rimosso.');
    }
}
