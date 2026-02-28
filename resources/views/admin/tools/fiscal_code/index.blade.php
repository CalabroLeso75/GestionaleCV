<x-app-layout>
    <x-slot name="header">
        {{ __('Strumenti Codice Fiscale') }}
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="row g-4">
                <!-- Section 1: Calculate CF -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-calculator me-2"></i> Calcola Codice Fiscale</h5>
                        </div>
                        <div class="card-body p-4">
                            <form id="calculate-form" class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nome</label>
                                    <input type="text" name="name" class="form-control" placeholder="es. Mario" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Cognome</label>
                                    <input type="text" name="surname" class="form-control" placeholder="es. Rossi" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Data di Nascita</label>
                                    <input type="date" name="birth_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Genere</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="male">Uomo</option>
                                        <option value="female">Donna</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold border-bottom pb-2">Luogo di Nascita</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="birth_type" id="birth_italy" value="italy" checked>
                                                <label class="form-check-label" for="birth_italy">Italia</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="birth_type" id="birth_abroad" value="abroad">
                                                <label class="form-check-label" for="birth_abroad">Estero</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="italy-selectors" class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label">Provincia</label>
                                        <select id="birth_province" class="form-select">
                                            <option value="">Caricamento...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Comune</label>
                                        <select id="birth_city" name="city_id" class="form-select" disabled>
                                            <option value="">Seleziona provincia</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="abroad-selectors" class="col-12 mt-3 d-none">
                                    <label class="form-label">Stato Estero</label>
                                    <select id="birth_country" name="country_id" class="form-select">
                                        <option value="">Caricamento...</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                                        Genera <i class="fas fa-magic ms-2"></i>
                                    </button>
                                </div>
                            </form>

                            <div id="result-cf" class="mt-4 p-4 bg-light rounded-3 text-center d-none border border-primary border-dashed">
                                <h6 class="text-uppercase text-muted small mb-2">Il tuo Codice Fiscale:</h6>
                                <div class="display-5 fw-bold text-primary font-monospace" id="cf-display"></div>
                                <button onclick="copyToClipboard('cf-display')" class="btn btn-sm btn-outline-primary mt-2 rounded-pill">
                                    <i class="far fa-copy me-1"></i> Copia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Reverse CF -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-search-location me-2"></i> Codice Fiscale Inverso</h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-4">Inserisci un Codice Fiscale per estrarre genere, data e luogo di nascita.</p>
                            <form id="reverse-form">
                                <div class="mb-3">
                                    <input type="text" name="cf" class="form-control form-control-lg font-monospace text-uppercase" placeholder="RSSMRA80A01H501W" maxlength="16" required>
                                </div>
                                <button type="submit" class="btn btn-dark btn-lg w-100 rounded-pill shadow-sm">
                                    Analizza <i class="fas fa-microscope ms-2"></i>
                                </button>
                            </form>

                            <div id="result-reverse" class="mt-4 d-none">
                                <div class="list-group list-group-flush border rounded-3 overflow-hidden shadow-sm">
                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                        <span class="text-muted"><i class="fas fa-venus-mars me-2"></i> Genere</span>
                                        <span class="fw-bold" id="rev-gender">-</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-calendar-alt me-2"></i> Data Nascita</span>
                                        <span class="fw-bold" id="rev-date">-</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                                        <span class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Luogo Nascita</span>
                                        <span class="fw-bold text-end" id="rev-place">-</span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-id-card me-2"></i> Cod. Catastale</span>
                                        <span class="fw-bold font-monospace" id="rev-cadastral">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load Location Data
        loadLocations();

        // Handle Birth Type Toggle
        document.querySelectorAll('input[name="birth_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Clear result on toggle
                document.getElementById('result-cf').classList.add('d-none');
                
                if (this.value === 'italy') {
                    document.getElementById('italy-selectors').classList.remove('d-none');
                    document.getElementById('abroad-selectors').classList.add('d-none');
                    document.getElementById('birth_city').required = true;
                    document.getElementById('birth_country').required = false;
                    // Reset abroad selection
                    document.getElementById('birth_country').value = '';
                } else {
                    document.getElementById('italy-selectors').classList.add('d-none');
                    document.getElementById('abroad-selectors').classList.remove('d-none');
                    document.getElementById('birth_city').required = false;
                    document.getElementById('birth_country').required = true;
                    // Reset italy selection
                    document.getElementById('birth_province').value = '';
                    document.getElementById('birth_city').innerHTML = '<option value="">Seleziona provincia</option>';
                    document.getElementById('birth_city').disabled = true;
                }
            });
        });

        // Hide result when any input in the form changes
        document.getElementById('calculate-form').querySelectorAll('input, select').forEach(element => {
            element.addEventListener('change', () => {
                document.getElementById('result-cf').classList.add('d-none');
            });
            if (element.tagName === 'INPUT') {
                element.addEventListener('input', () => {
                    document.getElementById('result-cf').classList.add('d-none');
                });
            }
        });

        // Calculate CF
        document.getElementById('calculate-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            fetch('{{ route("admin.tools.fiscal_code.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if (res.cf) {
                    document.getElementById('result-cf').classList.remove('d-none');
                    document.getElementById('cf-display').textContent = res.cf;
                } else {
                    alert('Errore: ' + res.error);
                }
            });
        });

        // Reverse CF
        document.getElementById('reverse-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('{{ route("admin.tools.fiscal_code.reverse") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cf: formData.get('cf') })
            })
            .then(res => res.json())
            .then(res => {
                if (res.error) {
                    alert('Errore: ' + res.error);
                } else {
                    document.getElementById('result-reverse').classList.remove('d-none');
                    document.getElementById('rev-gender').textContent = res.gender === 'male' ? 'Uomo' : 'Donna';
                    document.getElementById('rev-date').textContent = res.birth_date;
                    document.getElementById('rev-place').textContent = res.birth_place ? (res.birth_place.name + (res.birth_place.province_acronym ? ' (' + res.birth_place.province_acronym + ')' : '')) : 'Non trovato';
                    document.getElementById('rev-cadastral').textContent = res.cadastral_code;
                }
            });
        });
    });

    function loadLocations() {
        // Provinces
        fetch('{{ route("api.provinces") }}')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('birth_province');
                select.innerHTML = '<option value="">Seleziona provincia</option>';
                data.forEach(p => {
                    select.innerHTML += `<option value="${p.id}">${p.name}</option>`;
                });
            });

        // Province change -> Cities
        document.getElementById('birth_province').addEventListener('change', function() {
            const citySelect = document.getElementById('birth_city');
            if (!this.value) {
                citySelect.innerHTML = '<option value="">Seleziona provincia</option>';
                citySelect.disabled = true;
                return;
            }
            citySelect.disabled = false;
            citySelect.innerHTML = '<option value="">Caricamento...</option>';
            fetch(`{{ url('/api/cities') }}/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">Seleziona comune</option>';
                    data.forEach(c => {
                        citySelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
                    });
                });
        });

        // Countries
        fetch('{{ route("api.countries") }}')
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('birth_country');
                select.innerHTML = '<option value="">Seleziona stato</option>';
                data.forEach(c => {
                    select.innerHTML += `<option value="${c.id}">${c.name_it}</option>`;
                });
            });
    }

    function copyToClipboard(id) {
        const text = document.getElementById(id).textContent;
        navigator.clipboard.writeText(text).then(() => {
            alert('Copiato negli appunti!');
        });
    }
    </script>

    <style>
    .rounded-4 { border-radius: 1rem !important; }
    .border-dashed { border-style: dashed !important; }
    .font-monospace { font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace !important; }
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-3px); }
    </style>
</x-app-layout>
