<x-app-layout>
    <x-slot name="header">
        Il mio Profilo
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @if(session('status') === 'profile-updated')
                Profilo aggiornato con successo.
            @elseif(session('status') === 'password-updated')
                Password aggiornata con successo.
            @else
                {{ session('status') }}
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Informazioni Profilo -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <svg class="icon icon-white icon-sm me-1"><use href="/svg/sprites.svg#it-user"></use></svg>
                        Dati Personali
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th style="width:40%;" class="text-muted">Nome</th>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Cognome</th>
                                <td>{{ $user->surname }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Codice Fiscale</th>
                                <td>{{ $user->fiscal_code }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Data di Nascita</th>
                                <td>{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Genere</th>
                                <td>{{ $user->gender === 'male' ? 'Uomo' : ($user->gender === 'female' ? 'Donna' : '-') }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Tipologia</th>
                                <td>
                                    <span class="badge {{ $user->type === 'internal' ? 'bg-primary' : 'bg-info' }}">
                                        {{ $user->type === 'internal' ? 'Dipendente Interno' : 'Esterno' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Stato</th>
                                <td>
                                    <span class="badge {{ $user->status === 'active' ? 'bg-success' : ($user->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                            </tr>
                            @if($user->internal_employee_id)
                            <tr>
                                <th class="text-muted">Anagrafica</th>
                                <td>Collegato (ID: {{ $user->internal_employee_id }})</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ruoli e Permessi -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <svg class="icon icon-white icon-sm me-1"><use href="{{ asset('svg/sprites.svg') }}#it-locked"></use></svg>
                        Ruoli e Privilegi
                    </h5>
                </div>
                <div class="card-body">
                    @if($user->getRoleNames()->isNotEmpty())
                        <p class="mb-2"><strong>Ruoli assegnati:</strong></p>
                        <div class="mb-3">
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge bg-primary me-1 mb-1" style="font-size: 0.9em;">{{ ucfirst($role) }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Nessun ruolo assegnato.</p>
                    @endif

                    @if($user->getAllPermissions()->isNotEmpty())
                        <p class="mb-2"><strong>Permessi:</strong></p>
                        <div>
                            @foreach($user->getAllPermissions() as $perm)
                                <span class="badge bg-secondary me-1 mb-1">{{ $perm->name }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mt-2 mb-0">
                            @if($user->hasRole('super-admin'))
                                Accesso completo (Super Admin).
                            @else
                                Nessun permesso specifico assegnato.
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modifica Email -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modifica Email</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="name" value="{{ $user->name }}">

                        <div class="form-group mb-3">
                            <label for="email" class="active">Nuovo indirizzo email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email', $user->email) }}" required>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
                        </div>

                        <button type="submit" class="btn btn-outline-primary">
                            Aggiorna Email
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modifica Password -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cambia Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="current_password" class="active">Password Attuale</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-danger" />
                        </div>

                        <div class="form-group mb-3">
                            <label for="new_password" class="active">Nuova Password</label>
                            <input type="password" class="form-control" id="new_password" name="password" required>
                            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-danger" />
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="active">Conferma Nuova Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-danger" />
                        </div>

                        <button type="submit" class="btn btn-outline-primary">
                            Aggiorna Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Chiudi Profilo -->
        <div class="col-12 text-center">
            <a href="javascript:history.back()" class="btn btn-outline-secondary px-5">
                ← Chiudi
            </a>
        </div>
    </div>
</x-app-layout>
