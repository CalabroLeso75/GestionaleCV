<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\EmailRecipient;

class DosController extends Controller
{
    /**
     * Entry hub "Strumenti DOS"
     */
    public function index()
    {
        return view('dos.index');
    }

    /**
     * App Mobile View per "Gestione Incendio"
     */
    public function fireManagement()
    {
        return view('dos.fire_management');
    }

    /**
     * View Storico Rilevazioni
     */
    public function history()
    {
        $reports = \App\Models\EmergencyReport::with('user')->orderBy('created_at', 'desc')->get();
        return view('dos.history', compact('reports'));
    }

    /**
     * Endpoint per invio report Email a SOUP e COP
     */
    public function sendReport(Request $request)
    {
        $validated = $request->validate([
            'op_lat' => 'nullable|numeric',
            'op_lng' => 'nullable|numeric',
            'fire_lat' => 'required|numeric',
            'fire_lng' => 'required|numeric',
            'distance' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'wind_speed' => 'nullable|numeric',
            'wind_direction' => 'nullable|string',

            // Nuovi campi meteo
            'wind_forecast_2h_speed' => 'nullable|numeric',
            'wind_forecast_2h_dir' => 'nullable|string',
            'wind_forecast_2h_gust' => 'nullable|numeric',
            'wind_forecast_4h_speed' => 'nullable|numeric',
            'wind_forecast_4h_dir' => 'nullable|string',
            'wind_forecast_4h_gust' => 'nullable|numeric',
            'wind_forecast_6h_speed' => 'nullable|numeric',
            'wind_forecast_6h_dir' => 'nullable|string',
            'wind_forecast_6h_gust' => 'nullable|numeric',

            'municipality' => 'nullable|string',
            'province' => 'nullable|string',
            'notes' => 'nullable|string',
            'nearest_toponyms_json' => 'nullable|string',
            'toponym' => 'nullable|string',

            // Campi GIS
            'polygon_geojson' => 'nullable|string',
            'area_hectares' => 'nullable|numeric',
            'front_meters' => 'nullable|numeric',
            'kml_path' => 'nullable|string'
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $roleSnapshot = $user->hasRole('dos') ? 'DOS' : 'Operatore';

        // Estrai nome toponimo da dropdown o usa il primo json
        $chosenToponym = $request->input('toponym');
        if (!$chosenToponym && !empty($validated['nearest_toponyms_json'])) {
            $tArr = json_decode($validated['nearest_toponyms_json'], true);
            if(is_array($tArr) && count($tArr) > 0) $chosenToponym = $tArr[0]['name'] ?? null;
        }

        // Generazione KML
        $kmlPath = null;
        if (!empty($validated['polygon_geojson'])) {
            $geojsonArray = json_decode($validated['polygon_geojson'], true);
            if ($geojsonArray && isset($geojsonArray['geometry'])) {
                $geomType = $geojsonArray['geometry']['type'];
                $coords = $geojsonArray['geometry']['coordinates'];

                $kmlCoords = "";
                if ($geomType === 'Polygon') {
                    foreach($coords[0] as $point) {
                        $kmlCoords .= $point[0] . "," . $point[1] . ",0\n";
                    }
                    $kmlGeom = "<Polygon><outerBoundaryIs><LinearRing><coordinates>\n{$kmlCoords}</coordinates></LinearRing></outerBoundaryIs></Polygon>";
                } elseif ($geomType === 'LineString') {
                    foreach($coords as $point) {
                        $kmlCoords .= $point[0] . "," . $point[1] . ",0\n";
                    }
                    $kmlGeom = "<LineString><coordinates>\n{$kmlCoords}</coordinates></LineString>";
                }

                if (isset($kmlGeom)) {
                    $kmlContent = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2">
  <Document>
    <name>Perimetro Incendio DOS</name>
    <Style id="fireStyle">
      <LineStyle><color>ff0000ff</color><width>4</width></LineStyle>
      <PolyStyle><color>7f0000ff</color></PolyStyle>
    </Style>
    <Placemark>
      <name>Rilevamento GIS</name>
      <styleUrl>#fireStyle</styleUrl>
      ' . $kmlGeom . '
    </Placemark>
  </Document>
</kml>';
                    $filename = 'kml/report_' . time() . '_' . $user->id . '.kml';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $kmlContent);
                    $kmlPath = $filename;
                }
            }
        }

        // 1. Salva nel DB
        $report = \App\Models\EmergencyReport::create([
            'user_id' => $user->id,
            'role_snapshot' => $roleSnapshot,
            'op_lat' => $validated['op_lat'] ?? null,
            'op_lng' => $validated['op_lng'] ?? null,
            'fire_lat' => $validated['fire_lat'],
            'fire_lng' => $validated['fire_lng'],
            'distance' => $validated['distance'] ?? null,
            'municipality' => $validated['municipality'] ?? null,
            'province' => $validated['province'] ?? null,
            'toponym' => $chosenToponym,
            'temperature' => $validated['temperature'] ?? null,
            'wind_speed' => $validated['wind_speed'] ?? null,
            'wind_direction' => $validated['wind_direction'] ?? null,

            'wind_forecast_2h_speed' => $validated['wind_forecast_2h_speed'] ?? null,
            'wind_forecast_2h_dir' => $validated['wind_forecast_2h_dir'] ?? null,
            'wind_forecast_2h_gust' => $validated['wind_forecast_2h_gust'] ?? null,
            'wind_forecast_4h_speed' => $validated['wind_forecast_4h_speed'] ?? null,
            'wind_forecast_4h_dir' => $validated['wind_forecast_4h_dir'] ?? null,
            'wind_forecast_4h_gust' => $validated['wind_forecast_4h_gust'] ?? null,
            'wind_forecast_6h_speed' => $validated['wind_forecast_6h_speed'] ?? null,
            'wind_forecast_6h_dir' => $validated['wind_forecast_6h_dir'] ?? null,
            'wind_forecast_6h_gust' => $validated['wind_forecast_6h_gust'] ?? null,

            'notes' => $validated['notes'] ?? null,

            'polygon_geojson' => $validated['polygon_geojson'] ?? null,
            'area_hectares' => $validated['area_hectares'] ?? null,
            'front_meters' => $validated['front_meters'] ?? null,
            'kml_path' => $kmlPath,
        ]);

        \App\Services\ActivityLogger::log('report_incendio', 'EmergencyReport', $report->id, "Registrata nuova rilevazione emergenza #{$report->id} da {$roleSnapshot} {$user->name} {$user->surname}");

        // Get dynamic emails from DB (ignores province logic for now, gets all active)
        $soupEmails = EmailRecipient::getActiveEmailsByRole('soup');
        $copEmails = EmailRecipient::getActiveEmailsByRole('cop');

        // Fallbacks if DB is empty - but only if necessary, empty arrays for CC are fine.
        if (empty($soupEmails)) {
            $soupEmails = [env('SOUP_EMAIL', 'soup@calabriaverde.eu')];
        }
        // Do not force a fake COP email if none is set, it's better to just skip CC.

        try {
            $mailSubject = "Incendio " . ($report->municipality ?? 'Ignoto') . " (" . ($report->province ?? 'ND') . ") - " . ($report->toponym ?? 'Toponimo non specificato');

            Mail::send('emails.dos_report', ['data' => $validated, 'user' => $user, 'report' => $report], function($message) use ($soupEmails, $copEmails, $mailSubject, $report) {
                $message->to($soupEmails);

                if (!empty($copEmails)) {
                    $message->cc($copEmails);
                }

                $message->subject('🔥 URGENZA: ' . $mailSubject);

                if($report->kml_path && file_exists(storage_path('app/public/' . $report->kml_path))) {
                    $message->attach(storage_path('app/public/' . $report->kml_path), [
                        'as' => 'perimetro_incendio.kml',
                        'mime' => 'application/vnd.google-earth.kml+xml',
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Report Inviato con successo a SOUP e COP.', 'report_id' => $report->id]);
        } catch (\Exception $e) {
            Log::error('Errore invio email DOS: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Errore invio email.', 'error' => $e->getMessage()], 500);
        }
    }
}
