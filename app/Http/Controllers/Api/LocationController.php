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
        return response()->json(City::where('province_id', $province_id)->orderBy('name')->get());
    }

    public function getCountries()
    {
        return response()->json(Country::orderBy('name_it')->get());
    }
}
