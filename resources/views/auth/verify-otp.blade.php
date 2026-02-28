<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Grazie per la registrazione! Abbiamo inviato un codice OTP di 8 cifre alla tua email.') }}
        <br>
        <strong>Controlla la tua posta (anche Spam).</strong>
    </div>

    <form method="POST" action="{{ route('otp.verify.submit') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- OTP Code -->
        <div class="form-group">
            <label for="otp">Codice OTP (8 cifre)</label>
            <input type="text" class="form-control" id="otp" name="otp" required autofocus maxlength="8">
            <x-input-error :messages="$errors->get('otp')" class="mt-2 text-danger" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="btn btn-primary">
                {{ __('Verifica Codice') }}
            </button>
        </div>
    </form>
</x-guest-layout>
