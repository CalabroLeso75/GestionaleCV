<x-guest-layout>
    <h2 class="h3 text-center mb-4">Password Dimenticata</h2>

    <p class="text-muted text-center mb-4">
        Inserisci il tuo indirizzo email e ti invieremo un link per reimpostare la password.
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
            <label for="email" class="{{ old('email') ? 'active' : '' }}">Email</label>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('login') }}" class="text-decoration-underline">
                Torna al Login
            </a>

            <button type="submit" class="btn btn-primary">
                Invia Link di Reset
            </button>
        </div>
    </form>
</x-guest-layout>
