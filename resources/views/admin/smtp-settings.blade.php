<x-app-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="h3 mb-4">⚙️ Configurazione SMTP Email</h2>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>✅</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>❌</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- SMTP Configuration Form --}}
                <div class="card card-bg mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Parametri Server SMTP</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.smtp.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="MAIL_HOST" name="MAIL_HOST" 
                                               value="{{ old('MAIL_HOST', $settings['MAIL_HOST']) }}" required>
                                        <label for="MAIL_HOST" class="{{ $settings['MAIL_HOST'] ? 'active' : '' }}">Server SMTP</label>
                                        <small class="form-text text-muted">Es: smtps.aruba.it, smtp.gmail.com</small>
                                        <x-input-error :messages="$errors->get('MAIL_HOST')" class="mt-2 text-danger" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="MAIL_PORT" name="MAIL_PORT" 
                                               value="{{ old('MAIL_PORT', $settings['MAIL_PORT']) }}" required>
                                        <label for="MAIL_PORT" class="{{ $settings['MAIL_PORT'] ? 'active' : '' }}">Porta</label>
                                        <small class="form-text text-muted">465 (SSL) o 587 (STARTTLS)</small>
                                        <x-input-error :messages="$errors->get('MAIL_PORT')" class="mt-2 text-danger" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" 
                                               value="{{ old('MAIL_USERNAME', $settings['MAIL_USERNAME']) }}" required>
                                        <label for="MAIL_USERNAME" class="{{ $settings['MAIL_USERNAME'] ? 'active' : '' }}">Username (Email)</label>
                                        <x-input-error :messages="$errors->get('MAIL_USERNAME')" class="mt-2 text-danger" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" 
                                               value="{{ old('MAIL_PASSWORD', $settings['MAIL_PASSWORD']) }}">
                                        <label for="MAIL_PASSWORD" class="{{ $settings['MAIL_PASSWORD'] ? 'active' : '' }}">Password</label>
                                        <small class="form-text text-muted">Lascia vuoto per non modificare</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <select class="form-control" id="MAIL_SCHEME" name="MAIL_SCHEME" required>
                                            <option value="smtps" {{ ($settings['MAIL_SCHEME'] ?? '') == 'smtps' ? 'selected' : '' }}>SSL (smtps - porta 465)</option>
                                            <option value="smtp" {{ ($settings['MAIL_SCHEME'] ?? '') == 'smtp' ? 'selected' : '' }}>STARTTLS (smtp - porta 587)</option>
                                        </select>
                                        <label for="MAIL_SCHEME" class="active">Protocollo</label>
                                        <x-input-error :messages="$errors->get('MAIL_SCHEME')" class="mt-2 text-danger" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" 
                                               value="{{ old('MAIL_FROM_ADDRESS', str_replace('\"', '', $settings['MAIL_FROM_ADDRESS'])) }}" required>
                                        <label for="MAIL_FROM_ADDRESS" class="{{ $settings['MAIL_FROM_ADDRESS'] ? 'active' : '' }}">Email Mittente</label>
                                        <x-input-error :messages="$errors->get('MAIL_FROM_ADDRESS')" class="mt-2 text-danger" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" 
                                               value="{{ old('MAIL_FROM_NAME', str_replace('\"', '', $settings['MAIL_FROM_NAME'])) }}" required>
                                        <label for="MAIL_FROM_NAME" class="{{ $settings['MAIL_FROM_NAME'] ? 'active' : '' }}">Nome Mittente</label>
                                        <x-input-error :messages="$errors->get('MAIL_FROM_NAME')" class="mt-2 text-danger" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    💾 Salva Configurazione
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Provider Presets --}}
                <div class="card card-bg mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Preset Provider</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Clicca per auto-compilare i dati del server:</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="setPreset('smtps.aruba.it', 465, 'smtps')">
                                📧 Aruba (SSL/465)
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="setPreset('smtp.gmail.com', 587, 'smtp')">
                                📧 Gmail (STARTTLS/587)
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="setPreset('smtp.office365.com', 587, 'smtp')">
                                📧 Office 365 (STARTTLS/587)
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="setPreset('smtp.pec.aruba.it', 465, 'smtps')">
                                📧 Aruba PEC (SSL/465)
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Test Email Form --}}
                <div class="card card-bg">
                    <div class="card-header">
                        <h5 class="mb-0">🧪 Test Invio Email</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.smtp.test') }}">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <input type="email" class="form-control" id="test_email" name="test_email" required>
                                        <label for="test_email">Email di destinazione</label>
                                        <x-input-error :messages="$errors->get('test_email')" class="mt-2 text-danger" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100 mb-3">
                                        🚀 Invia Test
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setPreset(host, port, scheme) {
            document.getElementById('MAIL_HOST').value = host;
            document.getElementById('MAIL_PORT').value = port;
            document.getElementById('MAIL_SCHEME').value = scheme;

            // Trigger Bootstrap Italia label activation
            ['MAIL_HOST', 'MAIL_PORT'].forEach(function(id) {
                let label = document.querySelector('label[for="' + id + '"]');
                if (label) label.classList.add('active');
            });
        }
    </script>
</x-app-layout>
