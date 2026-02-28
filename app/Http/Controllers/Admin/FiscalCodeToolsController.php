<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Skills\FiscalCodeSkill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;

class FiscalCodeToolsController extends Controller
{
    protected $skill;

    public function __construct(FiscalCodeSkill $skill)
    {
        $this->skill = $skill;
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Allow super-admin
        if ($user->hasRole('super-admin')) {
            return view('admin.tools.fiscal_code.index');
        }

        // Allow HR staff
        $hasHRAccess = DB::table('user_area_roles')
            ->where('user_id', $user->id)
            ->where('area', 'Risorse Umane')
            ->exists();

        if (!$hasHRAccess) {
            abort(403);
        }

        return view('admin.tools.fiscal_code.index');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'city_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
        ]);

        $cadastralCode = null;
        if ($request->city_id) {
            $cadastralCode = DB::table('localizz_comune')->where('id', $request->city_id)->value('cadastral_code');
        } elseif ($request->country_id) {
            $cadastralCode = DB::table('localizz_statoestero')->where('id', $request->country_id)->value('cadastral_code');
        }

        if (!$cadastralCode) {
            return response()->json(['error' => 'Codice catastale non trovato per il luogo selezionato.'], 422);
        }

        try {
            $cf = $this->skill->calculate(
                $request->name,
                $request->surname,
                $request->gender,
                $request->birth_date,
                $cadastralCode
            );

            // Log activity
            ActivityLogger::log('calculate_cf', 'FiscalCode', null, "Calcolo codice fiscale per: {$request->name} {$request->surname} - Risultato: {$cf}");

            return response()->json(['cf' => $cf]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function reverse(Request $request)
    {
        $request->validate([
            'cf' => 'required|string|size:16',
        ]);

        try {
            $data = $this->skill->reverse($request->cf);

            // Log activity
            ActivityLogger::log('reverse_cf', 'FiscalCode', null, "Analisi inversa del codice fiscale: {$request->cf}");

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
