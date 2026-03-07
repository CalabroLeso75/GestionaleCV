<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\Province;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getProvinces()
    {
        // Calabria provinces only? Or all? User said "five Calabrian provinces" in previous context,
        // but for birth place it should probably be all Italian provinces.
        // Let's return all provinces sorted by name.
        return response()->json(Province::orderBy('name')->get());
    }

    public function getCities($province_id)
    {
        return response()->json(City::where('province_id', $province_id)->select('id', 'name', 'province_id')->orderBy('name')->get());
    }

    public function getCountries()
    {
        return response()->json(Country::orderBy('name_it')->get());
    }

    public function getNearestToponyms(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Coordinate mancanti'], 400);
        }

        // Calcola la distanza usando la formula di Haversine in SQL
        // Moltiplichiamo per 6371 (raggio terra in km)
        $toponyms = \App\Models\Toponym::selectRaw("
            name, latitude, longitude,
            ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) )
            * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distanza
        ", [$lat, $lon, $lat])
        ->having('distanza', '<', 50) // Limita a un raggio ragionevole (es. 50km) per ottimizzare
        ->orderBy('distanza')
        ->limit(20)
        ->get();

        return response()->json($toponyms);
    }

    public function getExactMunicipality(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Coordinate mancanti'], 400);
        }

        // MySQL 8+ with SRID 4326 expects POINT(lat lon), while MariaDB/older MySQL expects POINT(lon lat)
        $pointMariaDB = "POINT($lon $lat)";
        $pointMySQL8 = "POINT($lat $lon)";

        $city = City::with('province')
                    ->select('id', 'name', 'province_id')
                    ->whereNotNull('polygon')
                    ->whereRaw("(ST_Contains(polygon, ST_GeomFromText(?, 4326)) OR ST_Contains(polygon, ST_GeomFromText(?, 4326)))", [$pointMariaDB, $pointMySQL8])
                    ->first();

        if ($city) {
            return response()->json([
                'success' => true,
                'city_id' => $city->id,
                'city_name' => $city->name,
                'province_id' => $city->province_id,
                'province_name' => $city->province ? $city->province->name : null,
                'province_abbr' => $city->province ? $city->province->short_code : null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nessun comune trovato per queste coordinate'
        ], 404);
    }
}
