<x-app-layout>
    <x-slot name="header">
        Impostazioni Sito
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Info attuale -->
        <div class="col-12">
            <div class="card shadow-sm border-info">
                <div class="card-body">
                    <h6 class="mb-2">Configurazione attuale</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th style="width:200px;" class="text-muted">URL Sito</th>
                            <td><code>{{ config('app.url') }}</code></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Ambiente</th>
                            <td>
                                <span class="badge {{ config('app.env') === 'production' ? 'bg-success' : 'bg-warning' }}">
                                    {{ config('app.env') === 'production' ? '🌐 Produzione' : '🔧 Sviluppo locale' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Debug</th>
                            <td>
                                <span class="badge {{ config('app.debug') ? 'bg-warning' : 'bg-success' }}">
                                    {{ config('app.debug') ? 'Attivo (mostra errori)' : 'Disattivo (produzione)' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modifica -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Modifica Impostazioni</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.site.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-4">
                            <label for="APP_NAME" class="active"><strong>Nome Applicazione</strong></label>
                            <input type="text" class="form-control" id="APP_NAME" name="APP_NAME" 
                                   value="{{ old('APP_NAME', $settings['APP_NAME']) }}" required>
                            <small class="text-muted">Visualizzato nel titolo del browser e nelle email.</small>
                            @error('APP_NAME')
                                <span class="mt-1 text-danger d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="APP_URL" class="active"><strong>URL del Sito</strong></label>
                            <input type="url" class="form-control" id="APP_URL" name="APP_URL" 
                                   value="{{ old('APP_URL', $settings['APP_URL']) }}" required>
                            <small class="text-muted">
                                Questo URL viene usato per generare tutti i link (email, reset password, ecc.).<br>
                                <strong>Sviluppo:</strong> <code>http://localhost/GestionaleCV</code><br>
                                <strong>Produzione:</strong> <code>https://tuodominio.it/GestionaleCV</code>
                            </small>
                            @error('APP_URL')
                                <span class="mt-1 text-danger d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label class="active"><strong>Ambiente</strong></label>
                            <div class="mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="APP_ENV" id="env_local" 
                                           value="local" {{ $settings['APP_ENV'] === 'local' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="env_local">
                                        🔧 Sviluppo locale
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="APP_ENV" id="env_production" 
                                           value="production" {{ $settings['APP_ENV'] === 'production' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="env_production">
                                        🌐 Produzione
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted mt-1 d-block">
                                In <strong>Produzione</strong> il debug viene disattivato automaticamente per sicurezza.
                            </small>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <strong>⚠️ Attenzione:</strong> Dopo la modifica, tutti i link generati dal sistema 
                            (reset password, email di verifica, ecc.) useranno il nuovo URL.
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Salva Impostazioni
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Guida -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">📖 Guida</h6>
                </div>
                <div class="card-body" style="font-size: 0.9em;">
                    <p><strong>Quando cambiare l'URL?</strong></p>
                    <ul>
                        <li>Quando pubblichi il sito su un server esterno</li>
                        <li>Quando cambi dominio</li>
                        <li>Quando passi da HTTP a HTTPS</li>
                    </ul>
                    
                    <p class="mt-3"><strong>Cosa viene influenzato:</strong></p>
                    <ul>
                        <li>Link nelle email (reset password, OTP)</li>
                        <li>Redirect dopo login/registrazione</li>
                        <li>URL delle risorse del sito</li>
                    </ul>

                    <p class="mt-3"><strong>Esempio di configurazione produzione:</strong></p>
                    <code class="d-block p-2 bg-light rounded">https://gestionale.calabriaverde.eu</code>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
