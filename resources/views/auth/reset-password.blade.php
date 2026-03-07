<x-guest-layout>
    <div class="card card-bg shadow-sm">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="avatar-icon-wrapper mb-3 mx-auto" style="width: 60px; height: 60px; background-color: rgba(0, 102, 204, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg class="icon icon-primary" style="width: 32px; height: 32px;"><use href="{{ asset('svg/sprites.svg#it-locked') }}"></use></svg>
                </div>
                <h2 class="h3 font-weight-bold text-primary mb-2">Reimposta Password</h2>
                <p class="text-muted">
                    Scegli una nuova password sicura per il tuo account.
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" id="resetPasswordForm">
                @csrf

                <!-- Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="form-group mb-4">
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                    <label for="email" class="active">Indirizzo Email</label>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                </div>

                <!-- Nuova Password -->
                <div class="form-group mb-4">
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                    <label for="password">Nuova Password</label>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                </div>

                <!-- Conferma Password -->
                <div class="form-group mb-4">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    <label for="password_confirmation">Conferma Password</label>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
                </div>

                <div class="d-flex flex-column gap-3 mt-4">
                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                        <span class="submit-text">Reimposta Password</span>
                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true" id="submitSpinner"></span>
                    </button>
                    
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none small fw-bold">
                            <svg class="icon icon-xs icon-primary me-1"><use href="{{ asset('svg/sprites.svg#it-arrow-left') }}"></use></svg>
                            Annulla e torna al Login
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('resetPasswordForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');
            btn.setAttribute('disabled', 'true');
            spinner.classList.remove('d-none');
        });
    </script>
</x-guest-layout>
