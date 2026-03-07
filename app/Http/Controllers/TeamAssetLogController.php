<?php

namespace App\Http\Controllers;

use App\Models\TeamAssetLog;
use Illuminate\Http\Request;

class TeamAssetLogController extends Controller
{
    public function index(Request $request)
    {
        $query = TeamAssetLog::with(['team', 'user'])->orderBy('created_at', 'desc');

        // Optional filtering by sigla squadra
        if ($request->has('squadra') && $request->squadra != '') {
            $query->whereHas('team', function ($q) use ($request) {
                $q->where('sigla', 'like', '%' . $request->squadra . '%');
            });
        }

        // Optional filtering by asset type
        if ($request->has('asset_type') && $request->asset_type != '') {
            $query->where('asset_type', $request->asset_type);
        }

        $logs = $query->paginate(25);
        $logs->appends($request->all());

        return view('pc.aib.asset_logs.index', compact('logs'));
    }
}
