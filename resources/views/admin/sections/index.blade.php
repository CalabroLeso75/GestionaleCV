<x-app-layout>
    <x-slot name="header">
        Gestione Sezioni Dashboard
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form nuova sezione -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">➕ Nuova Sezione</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.sections.store') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="title" class="active">Titolo</label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder=" ">
                            <x-input-error :messages="$errors->get('title')" class="mt-1 text-danger" />
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="active">Descrizione</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder=" ">
                        </div>

                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="icon" class="active">Icona (emoji)</label>
                                    <input type="text" class="form-control text-center" id="icon" name="icon" value="📁" maxlength="4" style="font-size:1.5em;">
                                </div>
                            </div>
                            <div class="col-8">
                                <div class="form-group">
                                    <label for="color" class="active">Colore</label>
                                    <input type="color" class="form-control form-control-color w-100" id="color" name="color" value="#0066cc">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="route" class="active">URL o rotta (opzionale)</label>
                            <input type="text" class="form-control" id="route" name="route" placeholder=" ">
                            <small class="text-muted">Es: /area-risorse-umane o un URL completo</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="required_role" class="active">Ruolo richiesto (opzionale)</label>
                            <input type="text" class="form-control" id="required_role" name="required_role" placeholder=" ">
                            <small class="text-muted">Lascia vuoto per tutti gli utenti. Es: super-admin, admin</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Crea Sezione</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Elenco sezioni -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Sezioni esistenti</h5>
                </div>
                <div class="card-body p-0">
                    @forelse($sections as $section)
                        <div class="d-flex align-items-center p-3 border-bottom {{ !$section->is_active ? 'opacity-50' : '' }}">
                            <div class="me-3" style="font-size: 2em; width: 50px; text-align: center;">{{ $section->icon }}</div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">
                                    {{ $section->title }}
                                    @if(!$section->is_active)
                                        <span class="badge bg-secondary ms-1">Disattivata</span>
                                    @endif
                                </h6>
                                <small class="text-muted">
                                    {{ $section->description ?? '—' }}
                                    @if($section->required_role)
                                        · <span class="badge bg-dark">{{ $section->required_role }}</span>
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex gap-1">
                                <!-- Toggle attivo/disattivo -->
                                <form method="POST" action="{{ route('admin.sections.update', $section->id) }}" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="title" value="{{ $section->title }}">
                                    <input type="hidden" name="description" value="{{ $section->description }}">
                                    <input type="hidden" name="icon" value="{{ $section->icon }}">
                                    <input type="hidden" name="route" value="{{ $section->route }}">
                                    <input type="hidden" name="color" value="{{ $section->color }}">
                                    <input type="hidden" name="required_role" value="{{ $section->required_role }}">
                                    <input type="hidden" name="is_active" value="{{ $section->is_active ? '0' : '1' }}">
                                    <button type="submit" class="btn btn-sm {{ $section->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                            title="{{ $section->is_active ? 'Disattiva' : 'Attiva' }}">
                                        {{ $section->is_active ? '⏸' : '▶' }}
                                    </button>
                                </form>
                                <!-- Elimina -->
                                <form method="POST" action="{{ route('admin.sections.destroy', $section->id) }}" 
                                      class="d-inline" onsubmit="return confirm('Eliminare la sezione \'{{ $section->title }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">🗑</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">
                            Nessuna sezione personalizzata. Creane una usando il form a sinistra.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">← Torna all'Amministrazione</a>
    </div>
</x-app-layout>
