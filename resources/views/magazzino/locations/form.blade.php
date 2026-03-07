<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold text-dark mb-0">
            {{ isset($location) ? 'Modifica Ubicazione: ' . $location->name : 'Nuova Ubicazione' }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 600px;">
            <div class="card shadow-sm">
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $e)<p class="mb-0">{{ $e }}</p>@endforeach
                        </div>
                    @endif

                    <form action="{{ isset($location) ? route('magazzino.locations.update', $location) : route('magazzino.locations.store') }}" method="POST">
                        @csrf
                        @if(isset($location)) @method('PUT') @endif

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome Ubicazione *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $location->name ?? '') }}" placeholder="Es. Sede Centrale, Distretto 3, HUB Lamezia..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo *</label>
                            <select name="type" class="form-select" required>
                                <option value="">-- Seleziona tipo --</option>
                                @php
                                    $types = [
                                        'sede_centrale'       => '🏛️ Sede Centrale',
                                        'hub_centrale'        => '🏢 HUB Centrale',
                                        'magazzino_centrale'  => '📦 Magazzino Centrale',
                                        'distretto'           => '🌿 Distretto',
                                        'hub_distretto'       => '🏢 HUB Distretto',
                                        'magazzino_distretto' => '📦 Magazzino Distretto',
                                        'distaccamento'       => '🏕️ Distaccamento (SOUP, vivai, officine...)',
                                        'magazzino_locale'    => '📦 Magazzino Locale',
                                        'punto_consumo'       => '📌 Punto di Consumo/Assegnazione',
                                    ];
                                @endphp
                                @foreach($types as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $location->type ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Ubicazione Padre (opzionale)</label>
                            <select name="parent_id" class="form-select">
                                <option value="">-- Nessuna (radice) --</option>
                                @foreach($allLocations as $loc)
                                    @if(!isset($location) || $loc->id !== $location->id)
                                        <option value="{{ $loc->id }}" {{ old('parent_id', $location->parent_id ?? $parentId ?? null) == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }} ({{ $loc->type }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('magazzino.locations.index') }}" class="btn btn-outline-secondary">Annulla</a>
                            <button type="submit" class="btn btn-primary">
                                {{ isset($location) ? '💾 Aggiorna' : '✅ Crea Ubicazione' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
