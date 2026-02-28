<x-guest-layout>
    <h2 class="h3 text-center mb-4">Registrazione Utente</h2>

    <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate id="registrationForm">
        @csrf

        <!-- Nome e Cognome -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus>
                    <label for="name" class="{{ old('name') ? 'active' : '' }}">Nome</label>
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" class="form-control" id="surname" name="surname" value="{{ old('surname') }}" required>
                    <label for="surname" class="{{ old('surname') ? 'active' : '' }}">Cognome</label>
                    <x-input-error :messages="$errors->get('surname')" class="mt-2 text-danger" />
                </div>
            </div>
        </div>

        <!-- Genere -->
        <div class="form-group mt-2">
            <label class="active d-block mb-1"><strong>Genere</strong></label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male" {{ old('gender') == 'male' ? 'checked' : '' }}>
                <label class="form-check-label" for="gender_male">Uomo</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                <label class="form-check-label" for="gender_female">Donna</label>
            </div>
            <x-input-error :messages="$errors->get('gender')" class="mt-2 text-danger" />
        </div>

        <!-- Data di Nascita -->
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="form-group">
                    <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                    <label for="birth_date" class="active">Data di Nascita</label>
                    <x-input-error :messages="$errors->get('birth_date')" class="mt-2 text-danger" />
                </div>
            </div>
        </div>

        <!-- Luogo di Nascita -->
        <div class="form-group mt-2">
            <label class="active d-block mb-1"><strong>Luogo di Nascita</strong></label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="birth_type" id="birth_italy" value="italy" {{ old('birth_type', 'italy') == 'italy' ? 'checked' : '' }} onchange="toggleBirthPlace()">
                <label class="form-check-label" for="birth_italy">Nato in Italia</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="birth_type" id="birth_abroad" value="abroad" {{ old('birth_type') == 'abroad' ? 'checked' : '' }} onchange="toggleBirthPlace()">
                <label class="form-check-label" for="birth_abroad">Nato all'Estero</label>
            </div>
        </div>

        <div id="italy_section">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="birth_province" class="active">Provincia</label>
                        <select class="form-control" id="birth_province" onchange="loadCities(this.value)">
                            <option value="">Seleziona Provincia...</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="birth_city_id" class="active">Comune</label>
                        <select class="form-control" id="birth_city_id" name="birth_city_id">
                            <option value="">Seleziona prima la provincia...</option>
                        </select>
                        <x-input-error :messages="$errors->get('birth_city_id')" class="mt-2 text-danger" />
                    </div>
                </div>
            </div>
        </div>

        <div id="abroad_section" style="display:none;">
            <div class="form-group">
                <label for="birth_country_id" class="active">Stato Estero</label>
                <select class="form-control" id="birth_country_id" name="birth_country_id">
                    <option value="">Caricamento stati...</option>
                </select>
                <x-input-error :messages="$errors->get('birth_country_id')" class="mt-2 text-danger" />
            </div>
        </div>

        <!-- Codice Fiscale con validazione visiva -->
        <div class="form-group mt-3">
            <input type="text" class="form-control" id="fiscal_code" name="fiscal_code" value="{{ old('fiscal_code') }}" required maxlength="16" style="text-transform:uppercase">
            <label for="fiscal_code" class="{{ old('fiscal_code') ? 'active' : '' }}">Codice Fiscale</label>
            <div id="cf_feedback" class="mt-1" style="font-size:0.85em;"></div>
            <x-input-error :messages="$errors->get('fiscal_code')" class="mt-2 text-danger" />
        </div>

        <!-- Tipo Utente -->
        <div class="form-group mt-2">
             <select class="form-control" id="type" name="type" required>
                 <option value="">Seleziona...</option>
                 <option value="internal" {{ old('type') == 'internal' ? 'selected' : '' }}>Dipendente Calabria Verde</option>
                 <option value="external" {{ old('type') == 'external' ? 'selected' : '' }}>Esterno / Ditta / Altro</option>
             </select>
             <label for="type" class="active">Tipologia Utente</label>
             <x-input-error :messages="$errors->get('type')" class="mt-2 text-danger" />
        </div>

        <!-- Email & Password -->
        <div class="form-group mt-2">
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            <label for="email" class="{{ old('email') ? 'active' : '' }}">Email</label>
            <small class="form-text text-muted">Per i dipendenti: usare @calabriaverde.eu se possibile.</small>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password">
                    <label for="password">Password</label>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                    <label for="password_confirmation">Conferma Password</label>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger" />
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a class="text-decoration-underline" href="{{ route('login') }}">
                {{ __('Già registrato?') }}
            </a>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                {{ __('Registrati') }}
            </button>
        </div>
    </form>

    <script>
        // ====================================
        // Codice Fiscale - Validazione Client
        // ====================================

        const CF_MONTH_MAP = { 'A':1,'B':2,'C':3,'D':4,'E':5,'H':6,'L':7,'M':8,'P':9,'R':10,'S':11,'T':12 };
        const CF_MONTH_REVERSE = { 1:'A',2:'B',3:'C',4:'D',5:'E',6:'H',7:'L',8:'M',9:'P',10:'R',11:'S',12:'T' };

        const CF_ODD = {
            '0':1,'1':0,'2':5,'3':7,'4':9,'5':13,'6':15,'7':17,'8':19,'9':21,
            'A':1,'B':0,'C':5,'D':7,'E':9,'F':13,'G':15,'H':17,'I':19,'J':21,
            'K':2,'L':4,'M':18,'N':20,'O':11,'P':3,'Q':6,'R':8,'S':12,'T':14,
            'U':16,'V':10,'W':22,'X':25,'Y':24,'Z':23
        };
        const CF_EVEN = {
            '0':0,'1':1,'2':2,'3':3,'4':4,'5':5,'6':6,'7':7,'8':8,'9':9,
            'A':0,'B':1,'C':2,'D':3,'E':4,'F':5,'G':6,'H':7,'I':8,'J':9,
            'K':10,'L':11,'M':12,'N':13,'O':14,'P':15,'Q':16,'R':17,'S':18,'T':19,
            'U':20,'V':21,'W':22,'X':23,'Y':24,'Z':25
        };

        function extractConsonants(str) {
            return str.toUpperCase().replace(/[^A-Z]/g, '').replace(/[AEIOU]/g, '');
        }
        function extractVowels(str) {
            return str.toUpperCase().replace(/[^A-Z]/g, '').replace(/[^AEIOU]/g, '');
        }

        function computeSurnameCode(surname) {
            let cons = extractConsonants(surname);
            let vow = extractVowels(surname);
            let code = (cons + vow + 'XXX').substring(0, 3);
            return code;
        }

        function computeNameCode(name) {
            let cons = extractConsonants(name);
            if (cons.length >= 4) {
                return cons[0] + cons[2] + cons[3];
            }
            let vow = extractVowels(name);
            let code = (cons + vow + 'XXX').substring(0, 3);
            return code;
        }

        function computeCheckDigit(cf15) {
            let sum = 0;
            for (let i = 0; i < 15; i++) {
                let c = cf15[i];
                if ((i + 1) % 2 === 1) {
                    sum += CF_ODD[c] || 0;
                } else {
                    sum += CF_EVEN[c] || 0;
                }
            }
            return String.fromCharCode(65 + (sum % 26));
        }

        function validateCF() {
            let cf = document.getElementById('fiscal_code').value.toUpperCase().trim();
            let feedback = document.getElementById('cf_feedback');
            let messages = [];
            let hasError = false;

            if (cf.length === 0) {
                feedback.innerHTML = '';
                return;
            }

            if (cf.length < 16) {
                feedback.innerHTML = '<span style="color:#666;">⏳ Inserisci tutti i 16 caratteri...</span>';
                return;
            }

            // Format check
            if (!/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/.test(cf)) {
                feedback.innerHTML = '<span style="color:red;">❌ Formato codice fiscale non valido</span>';
                return;
            }

            // Check digit
            let expectedCheck = computeCheckDigit(cf.substring(0, 15));
            if (cf[15] !== expectedCheck) {
                messages.push('<span style="color:red;">❌ Carattere di controllo errato (atteso: ' + expectedCheck + ')</span>');
                hasError = true;
            } else {
                messages.push('<span style="color:green;">✅ Checksum</span>');
            }

            // Surname check
            let surname = document.getElementById('surname').value;
            if (surname.length > 0) {
                let expectedSurname = computeSurnameCode(surname);
                let actualSurname = cf.substring(0, 3);
                if (expectedSurname === actualSurname) {
                    messages.push('<span style="color:green;">✅ Cognome</span>');
                } else {
                    messages.push('<span style="color:red;">❌ Cognome (atteso: ' + expectedSurname + ', trovato: ' + actualSurname + ')</span>');
                    hasError = true;
                }
            }

            // Name check
            let name = document.getElementById('name').value;
            if (name.length > 0) {
                let expectedName = computeNameCode(name);
                let actualName = cf.substring(3, 6);
                if (expectedName === actualName) {
                    messages.push('<span style="color:green;">✅ Nome</span>');
                } else {
                    messages.push('<span style="color:red;">❌ Nome (atteso: ' + expectedName + ', trovato: ' + actualName + ')</span>');
                    hasError = true;
                }
            }

            // Birth date + Gender check
            let birthDate = document.getElementById('birth_date').value;
            let genderEl = document.querySelector('input[name="gender"]:checked');
            if (birthDate && genderEl) {
                let date = new Date(birthDate);
                let year = String(date.getFullYear() % 100).padStart(2, '0');
                let month = date.getMonth() + 1;
                let day = date.getDate();
                let gender = genderEl.value;

                let cfYear = cf.substring(6, 8);
                let cfMonthChar = cf[8];
                let cfDay = parseInt(cf.substring(9, 11));

                if (cfYear === year) {
                    messages.push('<span style="color:green;">✅ Anno</span>');
                } else {
                    messages.push('<span style="color:red;">❌ Anno (atteso: ' + year + ')</span>');
                    hasError = true;
                }

                let expectedMonthChar = CF_MONTH_REVERSE[month];
                if (cfMonthChar === expectedMonthChar) {
                    messages.push('<span style="color:green;">✅ Mese</span>');
                } else {
                    messages.push('<span style="color:red;">❌ Mese (atteso: ' + expectedMonthChar + ')</span>');
                    hasError = true;
                }

                let expectedDay = gender === 'female' ? day + 40 : day;
                if (cfDay === expectedDay) {
                    messages.push('<span style="color:green;">✅ Giorno/Genere</span>');
                } else {
                    messages.push('<span style="color:red;">❌ Giorno/Genere (atteso: ' + expectedDay + ')</span>');
                    hasError = true;
                }
            }

            // Show result
            let summaryIcon = hasError ? '⚠️' : '✅';
            let summaryColor = hasError ? '#dc3545' : '#28a745';
            let summaryText = hasError ? 'CF non coerente con i dati inseriti' : 'CF valido e coerente';

            feedback.innerHTML = '<div style="padding:8px; border-radius:4px; background:' + (hasError ? '#fff3cd' : '#d4edda') + '; border:1px solid ' + (hasError ? '#ffc107' : '#c3e6cb') + '">'
                + '<strong style="color:' + summaryColor + '">' + summaryIcon + ' ' + summaryText + '</strong><br>'
                + '<small>' + messages.join(' | ') + '</small>'
                + '</div>';
        }

        // ====================================
        // Event Listeners
        // ====================================

        document.addEventListener('DOMContentLoaded', function() {
            // Load Provinces
            fetch("{{ route('api.provinces') }}")
                .then(response => response.json())
                .then(data => {
                    let select = document.getElementById('birth_province');
                    data.forEach(item => {
                         let option = document.createElement('option');
                         option.value = item.id;
                         option.text = item.name;
                         select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading provinces:', error));

            // Load Countries
            fetch("{{ route('api.countries') }}")
                .then(response => response.json())
                .then(data => {
                    let select = document.getElementById('birth_country_id');
                    select.innerHTML = '<option value="">Seleziona Stato...</option>';
                    data.forEach(item => {
                         let option = document.createElement('option');
                         option.value = item.id;
                         option.text = item.name_it;
                         select.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading countries:', error));

            toggleBirthPlace();

            // CF validation triggers
            document.getElementById('fiscal_code').addEventListener('input', validateCF);
            document.getElementById('fiscal_code').addEventListener('blur', validateCF);
            document.getElementById('name').addEventListener('blur', validateCF);
            document.getElementById('surname').addEventListener('blur', validateCF);
            document.getElementById('birth_date').addEventListener('change', validateCF);
            document.querySelectorAll('input[name="gender"]').forEach(el => {
                el.addEventListener('change', validateCF);
            });
        });

        function toggleBirthPlace() {
            let checked = document.querySelector('input[name="birth_type"]:checked');
            if (!checked) return;
            let type = checked.value;
            document.getElementById('italy_section').style.display = type === 'italy' ? 'block' : 'none';
            document.getElementById('abroad_section').style.display = type === 'abroad' ? 'block' : 'none';
        }

        function loadCities(provinceId) {
            let select = document.getElementById('birth_city_id');
            select.innerHTML = '<option value="">Caricamento...</option>';

            if (!provinceId) {
                select.innerHTML = '<option value="">Seleziona prima la provincia...</option>';
                return;
            }

            let url = "{{ route('api.cities', ':id') }}".replace(':id', provinceId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    select.innerHTML = '<option value="">Seleziona Comune...</option>';
                    data.forEach(item => {
                         let option = document.createElement('option');
                         option.value = item.id;
                         option.text = item.name;
                         select.appendChild(option);
                    });
                });
        }
    </script>
</x-guest-layout>
