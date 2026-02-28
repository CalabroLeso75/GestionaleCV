<x-guest-layout>
    <h2 class="h3 text-center mb-4">Reimposta Password</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email -->
        <div class="form-group">
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            <label for="email" class="active">Email</label>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
        </div>

        <!-- Nuova Password -->
        <div class="form-group">
            <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
            <label for="password">Nuova Password</label>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
        </div>

        <!-- Conferma Password -->
        <div class="form-group">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            <label for="password_confirmation">Conferma Password</label>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="{{ route('login') }}" class="text-decoration-underline">
                Torna al Login
            </a>

            <button type="submit" class="btn btn-primary">
                Reimposta Password
            </button>
        </div>
    </form>
</x-guest-layout>
