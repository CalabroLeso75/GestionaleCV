<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OtpVerificationController extends Controller
{
    public function show(Request $request)
    {
        $email = session('auth.otp_email');

        if (!$email) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:8',
            'email' => 'required|email|exists:users,email' // Hidden field
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
             return back()->withErrors(['otp' => 'Utente non trovato.']);
        }

        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Codice OTP non valido.']);
        }

        if ($user->otp_expires_at < now()) {
             return back()->withErrors(['otp' => 'Codice OTP scaduto.']);
        }

        // OTP Valid
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->email_verified_at = now(); // Mark email as verified
        $user->save();

        // Login? No, user requested admin approval.
        // Redirect to a "Pending Approval" page.
        
        session()->forget('auth.otp_email');
        
        return view('auth.pending-approval');
    }
}
