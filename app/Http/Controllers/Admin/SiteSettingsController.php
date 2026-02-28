<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    /**
     * Show the site settings form.
     */
    public function index()
    {
        $settings = [
            'APP_NAME' => env('APP_NAME', 'Gestionale CV'),
            'APP_URL' => env('APP_URL', 'http://localhost'),
            'APP_ENV' => env('APP_ENV', 'local'),
            'APP_DEBUG' => env('APP_DEBUG', false),
        ];

        return view('admin.site-settings', compact('settings'));
    }

    /**
     * Update site settings in .env file.
     */
    public function update(Request $request)
    {
        $request->validate([
            'APP_NAME' => 'required|string|max:255',
            'APP_URL' => 'required|url|max:255',
            'APP_ENV' => 'required|in:local,production',
        ]);

        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $updates = [
            'APP_NAME' => '"' . $request->APP_NAME . '"',
            'APP_URL' => $request->APP_URL,
            'APP_ENV' => $request->APP_ENV,
            'APP_DEBUG' => $request->APP_ENV === 'local' ? 'true' : 'false',
        ];

        foreach ($updates as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);

        // Clear caches
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return redirect()->route('admin.site.index')
            ->with('success', 'Impostazioni aggiornate! L\'URL del sito è ora: ' . $request->APP_URL);
    }
}
