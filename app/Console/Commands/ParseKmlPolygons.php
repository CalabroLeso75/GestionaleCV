<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Location\City;

class ParseKmlPolygons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:comuni-kml';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Municipalities KML into localizz_comune database polygon column';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kmlPath = base_path('da eliminare/pages/Calabria Comuni.kml');
        if (!file_exists($kmlPath)) {
            $this->error("KML file not found at: {$kmlPath}");
            return Command::FAILURE;
        }

        $this->info("Loading KML file...");
        
        $xml = simplexml_load_file($kmlPath);
        if (!$xml) {
            $this->error("Failed to parse KML XML.");
            return Command::FAILURE;
        }

        // KML namespace is usually an issue, we'll register it or just use brute force string search for Placemarks
        $namespaces = $xml->getNamespaces(true);
        $kmlNs = isset($namespaces['']) ? $namespaces[''] : 'http://www.opengis.net/kml/2.2';
        
        $xml->registerXPathNamespace('kml', $kmlNs);
        
        $placemarks = $xml->xpath('//kml:Placemark');
        
        if (empty($placemarks)) {
            // Try without namespace
            $placemarks = $xml->xpath('//Placemark');
        }

        $this->info("Found " . count($placemarks) . " placemarks to process. Please wait...");

        $successCount = 0;
        $missingCount = 0;

        foreach ($placemarks as $placemark) {
            $nameNode = $placemark->xpath('.//kml:name') ?: $placemark->xpath('.//name');
            $name = $nameNode ? (string)$nameNode[0] : null;

            // Optional ExtendedData if name isn't directly inside Placemark -> name
            // In many KMLs, it's inside ExtendedData -> SimpleData name="NOME" / "name"
            if (!$name) {
                $simpleDataName = $placemark->xpath('.//kml:SimpleData[@name="NOME"]') ?: $placemark->xpath('.//SimpleData[@name="NOME"]');
                if (!$simpleDataName) {
                    $simpleDataName = $placemark->xpath('.//kml:SimpleData[@name="name"]') ?: $placemark->xpath('.//SimpleData[@name="name"]');
                }
                
                if ($simpleDataName) {
                    $name = (string)$simpleDataName[0];
                }
            }
            
            if (!$name) {
                continue;
            }
            
            $name = trim(strtoupper($name));

            // Extract coordinates
            $coordsNode = $placemark->xpath('.//kml:coordinates') ?: $placemark->xpath('.//coordinates');
            if (empty($coordsNode)) {
                continue;
            }

            // A Placemark might have multiple polygons (MultiPolygon in KML).
            // We'll collect all coordinate tags.
            $polygonsStr = [];
            foreach ($coordsNode as $coordTxt) {
                $rawCoords = trim((string)$coordTxt);
                if (empty($rawCoords)) continue;

                $pairs = preg_split('/\s+/', $rawCoords);
                $polyPoints = [];

                foreach ($pairs as $pair) {
                    $parts = explode(',', $pair);
                    if (count($parts) >= 2) {
                        $lon = (float)$parts[0];
                        $lat = (float)$parts[1];
                        // WKT format uses Space separation for point: "lon lat"
                        $polyPoints[] = "{$lon} {$lat}";
                    }
                }

                if (count($polyPoints) >= 3) {
                    // Ensure closed polygon
                    if ($polyPoints[0] !== $polyPoints[count($polyPoints) - 1]) {
                        $polyPoints[] = $polyPoints[0];
                    }
                    $polygonsStr[] = "((" . implode(', ', $polyPoints) . "))";
                }
            }

            if (empty($polygonsStr)) {
                continue;
            }

            // We create a MULTIPOLYGON WKT string
            $wktStr = "MULTIPOLYGON(" . implode(", ", $polygonsStr) . ")";

            // Find city inside our DB (localizz_comune)
            $city = City::whereRaw('UPPER(name) = ?', [$name])
                        ->orWhereRaw("UPPER(REPLACE(name, '''', '')) = ?", [str_replace("'", "", $name)])
                        ->first();

            if ($city) {
                // Update specific row with WKT to ST_GeomFromText
                DB::table('localizz_comune')
                  ->where('id', $city->id)
                  ->update([
                      'polygon' => DB::raw("ST_GeomFromText('{$wktStr}', 4326)") // SRID 4326 per GPS
                  ]);
                
                $successCount++;
            } else {
                $this->warn("City not found in our DB: {$name}");
                $missingCount++;
            }
        }

        $this->info("Import completed! Success: {$successCount}, Not found in DB: {$missingCount}");
        return Command::SUCCESS;
    }
}
