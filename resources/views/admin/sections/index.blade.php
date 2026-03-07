<x-app-layout>
    <x-slot name="header">
        Registro Navigazione — Gestione Pagine
        <style>
            .tree-node { border-left: 3px solid #dee2e6; margin-left: 8px; padding-left: 12px; }
            .level-1 { border-left-color: #0d6efd; }
            .level-2 { border-left-color: #198754; }
            .level-3 { border-left-color: #ffc107; }
            .level-4 { border-left-color: #6c757d; }
            .tree-item { transition: background 0.15s; border-radius: 8px; }
            .tree-item:hover { background: #f8f9fa; }
            .icon-preview { font-size: 2em; width: 50px; height: 50px; display:flex; align-items:center; justify-content:center; border-radius:10px; flex-shrink:0; }
            .emoji-grid { display:grid; grid-template-columns: repeat(auto-fill, 42px); gap:4px; max-height:280px; overflow-y:auto; }
            .emoji-btn { font-size:1.4em; border:none; background:none; cursor:pointer; padding:4px; border-radius:6px; transition:background 0.1s; }
            .emoji-btn:hover { background:#e9ecef; }
        </style>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">{{ $errors->first() }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">

        {{-- ─── LEFT: Form Crea / Modifica ──────────────────────────────── --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm sticky-top" style="top:80px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold" id="formTitle">➕ Nuova Voce</h5>
                    <button class="btn btn-sm btn-outline-secondary d-none" id="resetFormBtn" onclick="resetForm()">✕ Reset</button>
                </div>
                <div class="card-body">
                    <form method="POST" id="sectionForm" action="{{ route('admin.sections.store') }}">
                        @csrf
                        <div id="methodField"></div>

                        {{-- Icona --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Icona</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-preview border" id="iconPreview">📁</div>
                                <div class="flex-grow-1">
                                    <input type="text" name="icon" id="iconInput" class="form-control text-center fs-4" value="📁" maxlength="5" placeholder="Emoji">
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1 w-100" onclick="document.getElementById('emojiPickerModal').style.display='flex'">🔍 Cerca emoji...</button>
                                </div>
                            </div>
                        </div>

                        {{-- Titolo --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Titolo <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="fTitle" class="form-control" required>
                        </div>

                        {{-- Descrizione --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descrizione</label>
                            <input type="text" name="description" id="fDescription" class="form-control" placeholder="Breve descrizione per l'utente">
                        </div>

                        {{-- Padre --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Elemento padre</label>
                            <select name="parent_id" id="fParent" class="form-select">
                                <option value="">— Nessuno (L1 Modulo principale) —</option>
                                @foreach($potentialParents as $p)
                                    <option value="{{ $p->id }}" data-level="{{ $p->level }}">
                                        {{ str_repeat('↳ ', $p->level - 1) }}{{ $p->icon }} {{ $p->title }} (L{{ $p->level }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Il livello verrà calcolato automaticamente.</div>
                        </div>

                        {{-- Colore e Route --}}
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label fw-semibold">Colore</label>
                                <input type="color" name="color" id="fColor" class="form-control form-control-color w-100" value="#0d6efd">
                            </div>
                            <div class="col-8">
                                <label class="form-label fw-semibold">URL / Rotta</label>
                                <input type="text" name="route" id="fRoute" class="form-control" placeholder="/sezione o nome.rotta">
                            </div>
                        </div>

                        {{-- Ruolo e Area --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Ruolo richiesto</label>
                                <select name="required_role" id="fRole" class="form-select form-select-sm">
                                    <option value="">Tutti</option>
                                    <option value="super-admin">Super Admin</option>
                                    <option value="admin">Admin</option>
                                    <option value="hr-manager">HR Manager</option>
                                    <option value="magazzino">Magazzino</option>
                                    <option value="autoparco">Autoparco</option>
                                    <option value="pc">Protezione Civile</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Area richiesta</label>
                                <select name="required_area" id="fArea" class="form-select form-select-sm">
                                    <option value="">Nessuna</option>
                                    <option value="hr">Risorse Umane</option>
                                    <option value="magazzino">Magazzino</option>
                                    <option value="autoparco">Autoparco</option>
                                    <option value="pc">Protezione Civile</option>
                                </select>
                            </div>
                        </div>

                        {{-- Ordine e Attivo --}}
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Ordine</label>
                                <input type="number" name="sort_order" id="fOrder" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="fActive" value="1" checked>
                                    <label class="form-check-label" for="fActive">Attivo</label>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="edit_id" id="editId" value="">
                        <button type="submit" class="btn btn-primary w-100 fw-bold" id="submitBtn">💾 Salva Voce</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── RIGHT: Albero Gerarchico ─────────────────────────────────── --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">🌳 Registro Pagine</h5>
                    <span class="badge bg-secondary">{{ $tree->count() }} moduli L1</span>
                </div>
                <div class="card-body p-2">
                    @forelse($tree as $root)
                        @include('admin.sections._node', ['node' => $root, 'depth' => 0])
                    @empty
                        <div class="text-center text-muted py-5">
                            <div style="font-size:3em;">🌱</div>
                            <p class="mt-2">Nessuna voce ancora. Crea il primo <strong>Modulo L1</strong> usando il form a sinistra.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <x-back-button :url="route('admin.index')" label="← Amministrazione di Sistema" />

    {{-- ─── EMOJI PICKER MODAL ────────────────────────────────────────── --}}
    <div id="emojiPickerModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
        <div class="card shadow-lg" style="width:440px; max-width:95vw; max-height:90vh; overflow:hidden;">
            <div class="card-header d-flex align-items-center justify-content-between py-2">
                <strong>🔍 Scegli Emoji</strong>
                <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('emojiPickerModal').style.display='none'">✕</button>
            </div>
            <div class="card-body p-2">
                <input type="text" id="emojiSearch" class="form-control form-control-sm mb-2" placeholder="Cerca: macchina, persona, casa..." oninput="filterEmojis(this.value)">
                <div class="emoji-grid" id="emojiGrid"></div>
            </div>
        </div>
    </div>

    {{-- ─── EDIT MODAL ────────────────────────────────────────────────── --}}
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Modifica Voce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="editModalBody">
                    {{-- Filled dynamically --}}
                </div>
            </div>
        </div>
    </div>

    <script>
    // ── Emoji dataset ────────────────────────────────────────────────────
    const EMOJIS = [
        {e:'🏠',k:'casa home'},{e:'🏢',k:'edificio ufficio'},{e:'🏭',k:'fabbrica industria'},
        {e:'👤',k:'persona utente'},{e:'👥',k:'persone gruppo utenti'},{e:'👷',k:'operaio lavoro'},
        {e:'👨‍💼',k:'manager dirigente'},{e:'👩‍💼',k:'manager dirigente donna'},{e:'🧑‍🔧',k:'tecnico meccanico'},
        {e:'🚗',k:'auto macchina veicolo'},{e:'🚙',k:'suv jeep'},{e:'🚚',k:'camion furgone'},
        {e:'🚐',k:'minivan pulmino'},{e:'🚑',k:'ambulanza'},{e:'🚒',k:'autopompa pompieri'},
        {e:'🚜',k:'trattore agricoltura'},{e:'🏎️',k:'auto sportiva'},{e:'⛟',k:'autocarro'},
        {e:'📦',k:'pacco scatolone magazzino'},{e:'📫',k:'posta corriere'},{e:'🗃️',k:'archivio file'},
        {e:'📋',k:'appunti lista'},{e:'📄',k:'documento foglio'},{e:'📃',k:'pagina testo'},
        {e:'📊',k:'grafico statistiche'},{e:'📈',k:'andamento crescita'},{e:'📉',k:'calo discesa'},
        {e:'🗂️',k:'cartelle file'},{e:'📁',k:'cartella'},{e:'📂',k:'cartella aperta'},
        {e:'🔧',k:'chiave attrezzi manutenzione'},{e:'⚙️',k:'impostazioni ingranaggio'},
        {e:'🔩',k:'bullone vite'},{e:'🛠️',k:'strumenti attrezzatura'},
        {e:'🔒',k:'lucchetto sicurezza'},{e:'🔓',k:'aperto sbloccato'},
        {e:'🛡️',k:'scudo protezione sicurezza'},{e:'🔑',k:'chiave accesso'},
        {e:'💻',k:'computer laptop'},{e:'🖥️',k:'monitor desktop'},{e:'📱',k:'smartphone telefono'},
        {e:'🖨️',k:'stampante'},{e:'⌨️',k:'tastiera'},{e:'🖱️',k:'mouse'},
        {e:'📡',k:'antenna rete wifi'},{e:'🔌',k:'spina elettrica'},
        {e:'📞',k:'telefono fisso'},{e:'☎️',k:'telefono'},
        {e:'🌿',k:'natura verde foglia'},{e:'🌲',k:'albero foresta'},
        {e:'🌱',k:'pianta germoglio'},{e:'🏔️',k:'montagna'},
        {e:'🔥',k:'fuoco incendio'},{e:'💧',k:'acqua'},{e:'🌊',k:'onda mare'},
        {e:'⛑️',k:'casco protezione aib'},{e:'🧯',k:'estintore'},
        {e:'🚨',k:'allarme emergenza'},{e:'⚠️',k:'attenzione pericolo'},
        {e:'🆘',k:'sos emergenza'},{e:'🚧',k:'cantiere lavori'},
        {e:'📍',k:'posizione mappa luogo'},{e:'🗺️',k:'mappa'},{e:'🧭',k:'bussola'},
        {e:'📅',k:'calendario data'},{e:'🕐',k:'orologio orario'},
        {e:'✅',k:'ok completato'},{e:'❌',k:'errore no'},{e:'⏸',k:'pausa stop'},
        {e:'▶️',k:'play avvia'},{e:'🔄',k:'aggiorna rinnova'},{e:'♻️',k:'riciclo'},
        {e:'💰',k:'soldi denaro costo'},{e:'💳',k:'carta pagamento'},
        {e:'📧',k:'email posta'},{e:'📨',k:'messaggio'},
        {e:'🎯',k:'obiettivo target'},{e:'📌',k:'pin puntina'},
        {e:'📝',k:'modifica nota'},{e:'✏️',k:'matita modifica'},
        {e:'🗑️',k:'cestino elimina'},{e:'💾',k:'salva floppy'},
        {e:'🔍',k:'ricerca cerca'},{e:'🔎',k:'lente ingrandimento'},
        {e:'🏷️',k:'etichetta tag'},{e:'🔐',k:'sicurezza password'},
        {e:'👁️',k:'occhio visibilità'},{e:'📢',k:'annuncio notifica'},
        {e:'🛒',k:'carrello spesa ordine'},{e:'🏪',k:'negozio magazzino'},
        {e:'🏗️',k:'costruzione cantiere'},{e:'🧱',k:'muro mattone'},
        {e:'⚡',k:'fulmine energia elettricità'},{e:'💡',k:'idea lampadina'},
        {e:'🔔',k:'campanella notifica'},{e:'📜',k:'lista registro storico'},
        {e:'🎖️',k:'medaglia merito'},{e:'🏆',k:'trofeo premio'},
        {e:'👨‍🚒',k:'pompiere vigile fuoco'},{e:'🦺',k:'giubbotto sicurezza'},
        {e:'🌿',k:'calabria verde'},{e:'🌐',k:'web sito internet'},
    ];

    let filteredEmojis = [...EMOJIS];

    function buildEmojiGrid(list) {
        const grid = document.getElementById('emojiGrid');
        grid.innerHTML = '';
        list.forEach(({e, k}) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'emoji-btn';
            btn.title = k;
            btn.textContent = e;
            btn.onclick = () => selectEmoji(e);
            grid.appendChild(btn);
        });
    }

    function filterEmojis(q) {
        const lq = q.toLowerCase();
        filteredEmojis = EMOJIS.filter(({k}) => !q || k.includes(lq));
        buildEmojiGrid(filteredEmojis);
    }

    function selectEmoji(e) {
        document.getElementById('iconInput').value = e;
        document.getElementById('iconPreview').textContent = e;
        document.getElementById('emojiPickerModal').style.display = 'none';
    }

    // Init picker on page load
    document.addEventListener('DOMContentLoaded', () => {
        buildEmojiGrid(EMOJIS);

        // Sync icon preview as user types
        document.getElementById('iconInput').addEventListener('input', function() {
            document.getElementById('iconPreview').textContent = this.value || '📁';
        });
    });

    // ── Form utilities ────────────────────────────────────────────────────
    function editSection(data) {
        document.getElementById('formTitle').textContent = '✏️ Modifica Voce';
        document.getElementById('resetFormBtn').classList.remove('d-none');
        document.getElementById('submitBtn').textContent = '💾 Aggiorna Voce';

        const form = document.getElementById('sectionForm');
        form.action = data.updateUrl;

        // Inject PUT method
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('editId').value = data.id;
        document.getElementById('fTitle').value = data.title;
        document.getElementById('fDescription').value = data.description || '';
        document.getElementById('iconInput').value = data.icon || '📁';
        document.getElementById('iconPreview').textContent = data.icon || '📁';
        document.getElementById('fColor').value = data.color || '#0d6efd';
        document.getElementById('fRoute').value = data.route || '';
        document.getElementById('fRole').value = data.required_role || '';
        document.getElementById('fArea').value = data.required_area || '';
        document.getElementById('fOrder').value = data.sort_order || 0;
        document.getElementById('fActive').checked = data.is_active == 1;

        const parentSelect = document.getElementById('fParent');
        parentSelect.value = data.parent_id || '';

        // Scroll to form
        form.closest('.card').scrollIntoView({behavior: 'smooth', block: 'start'});
    }

    function resetForm() {
        document.getElementById('formTitle').textContent = '➕ Nuova Voce';
        document.getElementById('resetFormBtn').classList.add('d-none');
        document.getElementById('submitBtn').textContent = '💾 Salva Voce';
        document.getElementById('sectionForm').action = '{{ route("admin.sections.store") }}';
        document.getElementById('sectionForm').reset();
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('editId').value = '';
        document.getElementById('iconPreview').textContent = '📁';
        document.getElementById('fActive').checked = true;
    }
    </script>
</x-app-layout>
