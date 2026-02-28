<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SmtpSettingsController extends Controller
{
    /**
     * Show the SMTP settings form.
     */
    public function index()
    {
        // Read current settings from .env
        $settings = [
            'MAIL_MAILER' => env('MAIL_MAILER', 'smtp'),
            'MAIL_SCHEME' => env('MAIL_SCHEME', 'smtp'),
            'MAIL_HOST' => env('MAIL_HOST', ''),
            'MAIL_PORT' => env('MAIL_PORT', ''),
            'MAIL_USERNAME' => env('MAIL_USERNAME', ''),
            'MAIL_PASSWORD' => env('MAIL_PASSWORD') ? '********' : '',
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', ''),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME', ''),
        ];

        return view('admin.smtp-settings', compact('settings'));
    }

    /**
     * Update SMTP settings in .env file.
     */
    public function update(Request $request)
    {
        $request->validate([
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|integer',
            'MAIL_USERNAME' => 'required|string',
            'MAIL_FROM_ADDRESS' => 'required|email',
            'MAIL_FROM_NAME' => 'required|string',
            'MAIL_SCHEME' => 'required|in:smtp,smtps',
        ]);

        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $updates = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_SCHEME' => $request->MAIL_SCHEME,
            'MAIL_HOST' => $request->MAIL_HOST,
            'MAIL_PORT' => $request->MAIL_PORT,
            'MAIL_USERNAME' => $request->MAIL_USERNAME,
            'MAIL_FROM_ADDRESS' => '"' . $request->MAIL_FROM_ADDRESS . '"',
            'MAIL_FROM_NAME' => '"' . $request->MAIL_FROM_NAME . '"',
        ];

        // Only update password if provided (not the masked value)
        if ($request->MAIL_PASSWORD && $request->MAIL_PASSWORD !== '********') {
            $updates['MAIL_PASSWORD'] = $request->MAIL_PASSWORD;
        }

        // Remove old MAIL_ENCRYPTION if present (deprecated in Laravel 12)
        $envContent = preg_replace("/^MAIL_ENCRYPTION=.*\n?/m", "", $envContent);

        foreach ($updates as $key => $value) {
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);

        // Clear config cache
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return redirect()->route('admin.smtp.index')
            ->with('success', 'Impostazioni SMTP aggiornate con successo! Riavvia l\'applicazione per applicare le modifiche.');
    }

    /**
     * Send a test email to verify SMTP configuration.
     */
    public function test(Request $request)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            Mail::raw(
                "Questo è un messaggio di test dal Gestionale Calabria Verde.\n\nSe ricevi questa email, la configurazione SMTP è corretta.\n\nData: " . now()->format('d/m/Y H:i:s'),
                function ($message) use ($request) {
                    $message->to($request->test_email)
                        ->subject('Test SMTP - Gestionale Calabria Verde');
                }
            );

            Log::info("Test email sent successfully to {$request->test_email}");

            return redirect()->route('admin.smtp.index')
                ->with('success', "Email di test inviata con successo a {$request->test_email}!");
        } catch (\Exception $e) {
            Log::error("Test email failed: " . $e->getMessage());

            return redirect()->route('admin.smtp.index')
                ->with('error', 'Errore invio email: ' . $e->getMessage());
        }
    }
}
