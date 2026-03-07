<x-app-layout>
    <x-slot name="header">
        Gestione Profili &amp; Pacchetti Sezioni
        <style>
            .role-card { transition:box-shadow 0.2s; }
            .role-card:hover { box-shadow:0 4px 20px rgba(0,0,0,0.12); }
            .section-pill { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:20px; font-size:0.8em; cursor:pointer; transition:opacity 0.15s; border:1px solid transparent; }
            .section-pill.assigned { background:#d1e7dd; border-color:#a3cfbb; color:#0a3622; }
            .section-pill.unassigned { background:#f8f9fa; border-color:#dee2e6; color:#495057; }
            .section-pill:hover { opacity:0.75; }
            .hierarchy-badge { font-size:0.7em; }
        </style>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- ─── INFO HIERARCHY ────────────────────────────────────────── --}}
    <div class="alert alert-info d-flex gap-2 align-items-start mb-4" role="alert">
        <span class="fs-4">ℹ️</span>
        <div>
            <strong>Come funziona la visibilità:</strong>
            <ul class="mb-0 mt-1">
                <li><strong>super-admin</strong> → vede <em>TUTTE</em> le tessere senza eccezioni</li>
                <li><strong>admin / amministratore di sistema</strong> → vede tutto tranne le tessere con ruolo <code>super-admin</code></li>
                <li><strong>hr-manager / operatori</strong> → vedono solo le sezioni loro esplicitamente assegnate</li>
                <li><strong>Pacchetti:</strong> cliccando sulle sezioni le aggiungi/rimuovi dal profilo del ruolo — gli utenti con quel ruolo le vedranno istantaneamente nella dashboard</li>
            </ul>
        </div>
    </div>

    <div class="row g-4">
        @foreach($roles as $role)
        @php
            $level = $hierarchy[strtolower($role->name)] ?? 0;
            $isSuperAdmin = strtolower($role->name) === 'super-admin';
            $isAdmin = in_array(strtolower($role->name), ['admin', 'amministratore di sistema']);
            $levelColor = match(true) {
                $isSuperAdmin => 'danger',
                $level >= 8   => 'warning',
                $level >= 2   => 'success',
                default       => 'secondary',
            };
        @endphp
        <div class="col-12">
            <div class="card shadow-sm role-card border-0">
                <div class="card-header d-flex align-items-center justify-content-between py-2"
                     style="border-left: 4px solid var(--bs-{{ $levelColor }});">
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <strong class="fs-6">{{ ucfirst($role->name) }}</strong>
                            <span class="badge bg-{{ $levelColor }} ms-2 hierarchy-badge">Livello {{ $level }}</span>
                            @if($isSuperAdmin)
                                <span class="badge bg-danger ms-1 hierarchy-badge">⭐ Privilegio Massimo — accesso totale</span>
                            @elseif($isAdmin)
                                <span class="badge bg-warning text-dark ms-1 hierarchy-badge">🔧 Admin — tutto tranne super-admin</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary">{{ $role->users_count }} utenti</span>
                        <span class="badge bg-primary" id="count-{{ $role->id }}">{{ count($role->section_ids) }} sezioni</span>
                        @if(!$isSuperAdmin)
                        <form method="POST" action="{{ route('admin.profiles.syncSections', $role->id) }}" id="syncForm-{{ $role->id }}">
                            @csrf
                            <div id="hiddenInputs-{{ $role->id }}"></div>
                            <button type="submit" class="btn btn-sm btn-primary">💾 Salva Pacchetto</button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body py-3">
                    @if($isSuperAdmin)
                        <div class="text-muted fst-italic">Il super-admin vede automaticamente <strong>tutte</strong> le sezioni attive. Non è possibile restringere l'accesso.</div>
                    @else
                        <div class="mb-2 text-muted small">Clicca una sezione per aggiungerla o rimuoverla dal pacchetto. <strong>Verde</strong> = inclusa, <strong>Grigia</strong> = non inclusa.</div>

                        {{-- Group by level for readability --}}
                        @foreach($allSections->groupBy('level') as $lvl => $sections)
                        <div class="mb-2">
                            <span class="text-muted fw-semibold" style="font-size:0.75em; text-transform:uppercase; letter-spacing:0.05em;">
                                {{ ['1'=>'🏠 Moduli (L1)','2'=>'📂 Sezioni (L2)','3'=>'📄 Sotto-sezioni (L3)','4'=>'🔗 Pagine (L4)'][$lvl] ?? 'L'.$lvl }}
                            </span>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach($sections as $section)
                                @php
                                    $isAssigned = in_array($section->id, $role->section_ids);
                                    $inputId = "sec_{$role->id}_{$section->id}";
                                @endphp
                                <label for="{{ $inputId }}" class="section-pill {{ $isAssigned ? 'assigned' : 'unassigned' }}"
                                       id="pill-{{ $role->id }}-{{ $section->id }}"
                                       onclick="togglePill({{ $role->id }}, {{ $section->id }})">
                                    <input type="checkbox" id="{{ $inputId }}" class="d-none"
                                           data-role="{{ $role->id }}" data-section="{{ $section->id }}"
                                           {{ $isAssigned ? 'checked' : '' }}>
                                    <span>{{ $section->icon }}</span>
                                    <span>{{ $section->title }}</span>
                                    @if($section->required_role)
                                        <span class="badge bg-dark" style="font-size:0.6em;">{{ $section->required_role }}</span>
                                    @endif
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <x-back-button :url="route('admin.index')" label="← Amministrazione di Sistema" />

    <script>
    function togglePill(roleId, sectionId) {
        const pill = document.getElementById(`pill-${roleId}-${sectionId}`);
        const cb   = document.getElementById(`sec_${roleId}_${sectionId}`);
        const now  = pill.classList.toggle('assigned');
        pill.classList.toggle('unassigned', !now);
        cb.checked = now;
        // Update the hidden inputs for the sync form
        rebuildHiddenInputs(roleId);
    }

    function rebuildHiddenInputs(roleId) {
        const container = document.getElementById(`hiddenInputs-${roleId}`);
        container.innerHTML = '';
        document.querySelectorAll(`input[data-role="${roleId}"]:checked`).forEach(cb => {
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'section_ids[]';
            input.value = cb.dataset.section;
            container.appendChild(input);
        });
        // Update badge count
        const count = document.querySelectorAll(`input[data-role="${roleId}"]:checked`).length;
        document.getElementById(`count-${roleId}`).textContent = count + ' sezioni';
    }

    // Init hidden inputs on load for each role
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[type=checkbox][data-role]').forEach(cb => {
            if (cb.checked) {
                const roleId = cb.dataset.role;
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'section_ids[]';
                input.value = cb.dataset.section;
                document.getElementById(`hiddenInputs-${roleId}`)?.appendChild(input);
            }
        });
    });
    </script>
</x-app-layout>
