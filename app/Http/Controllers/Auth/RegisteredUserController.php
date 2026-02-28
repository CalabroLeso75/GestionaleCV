<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpVerificationMail;
use App\Models\User;
use App\Models\InternalEmployee;
use App\Models\ExternalEmployee;
use App\Rules\CodiceFiscaleValidator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'fiscal_code' => ['required', 'string', 'size:16', 'unique:'.User::class, new CodiceFiscaleValidator()],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'birth_date' => ['required', 'date'],
            'birth_type' => ['required', 'in:italy,abroad'],
            'type' => ['required', 'in:internal,external'],
            'birth_city_id' => ['required_if:birth_type,italy', 'nullable', 'exists:localizz_comune,id'],
            'birth_country_id' => ['required_if:birth_type,abroad', 'nullable', 'exists:localizz_statoestero,id'],
        ]);

        // Generate 8-digit OTP
        $otp = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        $fiscalCode = strtoupper($request->fiscal_code);

        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'gender' => $request->gender,
            'fiscal_code' => $fiscalCode,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'birth_city_id' => $request->birth_type === 'italy' ? $request->birth_city_id : null,
            'birth_country_id' => $request->birth_type === 'abroad' ? $request->birth_country_id : null,
            'type' => $request->type,
            'status' => 'pending',
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        // ==========================================
        // MATCHING ANAGRAFICA: cerca by CF
        // ==========================================
        $matchType = null;
        $matchId = null;
        $matchInfo = null;

        // 1. Cerca in internal_employees
        $internalMatch = InternalEmployee::where('tax_code', $fiscalCode)->first();
        if ($internalMatch) {
            $matchType = 'internal';
            $matchId = $internalMatch->id;
            $matchInfo = "{$internalMatch->first_name} {$internalMatch->last_name} (Matricola: {$internalMatch->badge_number})";

            // Link user to internal employee
            $user->update([
                'internal_employee_id' => $internalMatch->id,
                'type' => 'internal', // Override type if CF matches internal
            ]);

            // Link internal employee to user
            $internalMatch->update(['user_id' => $user->id]);

            Log::info("Registrazione: CF {$fiscalCode} → match INTERNO #{$internalMatch->id} ({$matchInfo})");
        }

        // 2. Se non trovato internamente, cerca in external_employees
        if (!$matchType) {
            $externalMatch = ExternalEmployee::where('tax_code', $fiscalCode)->first();
            if ($externalMatch) {
                $matchType = 'external';
                $matchId = $externalMatch->id;
                $matchInfo = "{$externalMatch->first_name} {$externalMatch->last_name}";

                $user->update(['type' => 'external']);

                Log::info("Registrazione: CF {$fiscalCode} → match ESTERNO #{$externalMatch->id} ({$matchInfo})");
            }
        }

        // 3. Nessun match trovato
        if (!$matchType) {
            Log::info("Registrazione: CF {$fiscalCode} → nessun match in anagrafica. Utente in attesa di approvazione.");
        }

        // Store match info in session for OTP page feedback
        if ($matchType) {
            session(['registration.match' => [
                'type' => $matchType,
                'id' => $matchId,
                'info' => $matchInfo,
            ]]);
        }

        event(new Registered($user));

        // Send OTP via email
        try {
            Mail::to($user->email)->send(new OtpVerificationMail($otp, $user->name));
            Log::info("OTP email sent to {$user->email}");
        } catch (\Exception $e) {
            Log::warning("Failed to send OTP email to {$user->email}: " . $e->getMessage());
            Log::info("OTP for {$user->email}: $otp");
        }

        // DO NOT LOGIN - user needs OTP verification + admin approval
        session(['auth.otp_email' => $user->email]);

        return redirect()->route('otp.verify');
    }
}