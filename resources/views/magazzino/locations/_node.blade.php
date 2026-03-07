@php
    $typeLabels = [
        'sede_centrale'      => ['label' => 'Sede Centrale',      'color' => '#1565c0', 'icon' => '🏛️'],
        'hub_centrale'       => ['label' => 'HUB Centrale',       'color' => '#2e7d32', 'icon' => '🏢'],
        'magazzino_centrale' => ['label' => 'Magazzino Centrale', 'color' => '#f57c00', 'icon' => '📦'],
        'distretto'          => ['label' => 'Distretto',          'color' => '#6a1b9a', 'icon' => '🌿'],
        'hub_distretto'      => ['label' => 'HUB Distretto',      'color' => '#2e7d32', 'icon' => '🏢'],
        'magazzino_distretto'=> ['label' => 'Magazzino Distretto','color' => '#f57c00', 'icon' => '📦'],
        'distaccamento'      => ['label' => 'Distaccamento',      'color' => '#d32f2f', 'icon' => '🏕️'],
        'magazzino_locale'   => ['label' => 'Magazzino Locale',   'color' => '#f57c00', 'icon' => '📦'],
        'punto_consumo'      => ['label' => 'Punto di Consumo',   'color' => '#546e7a', 'icon' => '📌'],
    ];
    $meta = $typeLabels[$location->type] ?? ['label' => $location->type, 'color' => '#666', 'icon' => '📄'];
    $indent = $depth * 24;
@endphp

<div class="list-group-item py-2 px-3 d-flex justify-content-between align-items-center" style="padding-left: {{ $indent + 12 }}px !important;">
    <div class="d-flex align-items-center gap-2">
        @if($depth > 0)
            <span class="text-muted" style="font-size: 0.8em;">{{ str_repeat('│ ', $depth - 1) }}└─</span>
        @endif
        <span class="badge rounded-pill" style="background: {{ $meta['color'] }}; font-size: 0.8em;">{{ $meta['icon'] }} {{ $meta['label'] }}</span>
        <strong>{{ $location->name }}</strong>
        @if($location->children->count() > 0)
            <span class="badge bg-light text-muted border">{{ $location->children->count() }} sotto</span>
        @endif
    </div>
    <div class="d-flex gap-1">
        <a href="{{ route('magazzino.locations.create', ['parent_id' => $location->id]) }}" class="btn btn-sm btn-outline-success" title="Aggiungi sotto-ubicazione">+ Figlio</a>
        <a href="{{ route('magazzino.locations.edit', $location) }}" class="btn btn-sm btn-outline-primary" title="Modifica">✏️</a>
        <form action="{{ route('magazzino.locations.destroy', $location) }}" method="POST" class="d-inline" onsubmit="return confirm('Eliminare {{ $location->name }}?');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Elimina">🗑️</button>
        </form>
    </div>
</div>

@foreach($location->children as $child)
    @include('magazzino.locations._node', ['location' => $child, 'depth' => $depth + 1])
@endforeach
