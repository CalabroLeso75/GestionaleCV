<x-guest-layout>
    <h2 class="h3 text-center mb-4">Accedi</h2>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder=" ">
            <label for="email">Email</label>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password" placeholder=" ">
            <label for="password">Password</label>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
        </div>

        <!-- Remember Me -->
        <div class="form-check">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Ricordami</label>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            @if (Route::has('password.request'))
                <a class="text-decoration-underline" href="{{ route('password.request') }}">
                    Password dimenticata?
                </a>
            @endif

            <button type="submit" class="btn btn-primary">
                Accedi
            </button>
        </div>
    </form>
</x-guest-layout>
