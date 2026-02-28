<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AIArchitectController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->hasRole('super-admin')) {
            abort(403);
        }
        return view('admin.ai.architect');
    }

    public function runAudit(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->hasRole('super-admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $type = $request->input('type', 'db_performance');
        $model = "llama3";
        $prompt = "";

        if ($type === 'db_performance') {
            $schema = $this->getCoreSchema();
            $prompt = "Analizza lo schema database e suggerisci indici per ottimizzare le performance.\nSCHEMA:\n" . json_encode($schema, JSON_PRETTY_PRINT);
        } elseif ($type === 'code_quality') {
            $prompt = "Analizza l'architettura generale di un sistema Laravel per gestione risorse umane e forestali. Suggerisci migliori pratiche per la sicurezza dei dati sensibili.";
        }

        try {
            $response = Http::timeout(180)->post('http://localhost:11434/api/generate', [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false
            ]);

            if ($response->successful()) {
                return response()->json(['report' => $response->json()['response']]);
            }
            return response()->json(['error' => 'Ollama error: ' . $response->body()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection failed: ' . $e->getMessage()], 500);
        }
    }

    private function getCoreSchema()
    {
        $tables = ['users', 'internal_employees', 'external_employees', 'organizations', 'activity_logs'];
        $schema = [];
        foreach ($tables as $table) {
            try {
                $schema[$table] = DB::select("DESCRIBE `$table`");
            } catch (\Exception $e) {}
        }
        return $schema;
    }
}
