<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Location\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class IncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::with('comune.province')
            ->where('stato', '!=', 'Chiuso')
            ->orderByRaw("FIELD(priorita, 'Critica', 'Molto Alta', 'Alta', 'Media', 'Bassa')")
            ->orderBy('data_ora', 'desc')
            ->get();
            
        return view('pc.emergencies.index', compact('incidents'));
    }

    public function importForm()
    {
        return view('pc.emergencies.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($path));

        $header = array_shift($data);
        $importedCount = 0;

        foreach ($data as $row) {
            if (count($row) < count($header)) continue;
            
            $rowData = array_combine($header, $row);
            
            // Example mapping for PC2 CSV (to be adjusted based on real sample)
            // Assuming columns: ID_PCM, Data, Comune, Indirizzo, Tipo, Priorita, Note
            
            $comuneName = $rowData['Comune'] ?? null;
            $comune = City::where('name', 'LIKE', '%' . $comuneName . '%')->first();
            
            if (!$comune) continue; // Skip if comune not found

            Incident::updateOrCreate(
                ['pcm_incidente_id' => $rowData['ID_PCM'] ?? null],
                [
                    'codice_incidente' => Incident::generateCode(),
                    'data_ora' => Carbon::parse($rowData['Data'] ?? now()),
                    'comune_id' => $comune->id,
                    'indirizzo' => $rowData['Indirizzo'] ?? null,
                    'tipo_evento' => $rowData['Tipo'] ?? 'Generico',
                    'priorita' => $rowData['Priorita'] ?? 'Media',
                    'descrizione' => $rowData['Note'] ?? null,
                    'stato' => 'Aperto',
                ]
            );
            $importedCount++;
        }

        return redirect()->route('pc.emergencies.index')->with('success', "Importazione completata: $importedCount incidenti elaborati.");
    }
}
