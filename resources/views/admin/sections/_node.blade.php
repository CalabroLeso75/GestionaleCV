{{-- Recursive tree node for admin/sections/index --}}
@php
    $levelClass = 'level-' . $node->level;
    $indent = $depth * 16;
    $labelColors = ['1' => 'primary', '2' => 'success', '3' => 'warning', '4' => 'secondary'];
    $badgeColor = $labelColors[$node->level] ?? 'secondary';
@endphp

<div class="tree-node {{ $depth > 0 ? $levelClass : '' }} mb-1" style="{{ $depth > 0 ? 'margin-left:' . $indent . 'px' : '' }}">
    <div class="tree-item d-flex align-items-start gap-2 p-2 {{ !$node->is_active ? 'opacity-50' : '' }}">

        {{-- Expand/collapse toggle if has children --}}
        @if($node->childrenRecursive->count() > 0)
            <button class="btn btn-xs btn-outline-secondary p-0" style="width:22px;height:22px;font-size:0.7em;flex-shrink:0;margin-top:14px;"
                    onclick="toggleChildren({{ $node->id }})">▼</button>
        @else
            <div style="width:22px;flex-shrink:0;"></div>
        @endif

        {{-- Icon avec color dot --}}
        <div style="width:38px;height:38px;background:{{ $node->color }};border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.3em;flex-shrink:0;">{{ $node->icon }}</div>

        {{-- Info --}}
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center flex-wrap gap-1">
                <strong class="text-truncate">{{ $node->title }}</strong>
                <span class="badge bg-{{ $badgeColor }}">L{{ $node->level }}</span>
                @if(!$node->is_active)<span class="badge bg-dark">Inattivo</span>@endif
                @if($node->required_role)<span class="badge bg-dark text-white small">{{ $node->required_role }}</span>@endif
                @if($node->required_area)<span class="badge bg-info text-dark small">area:{{ $node->required_area }}</span>@endif
            </div>
            @if($node->description)
                <div class="text-muted small text-truncate">{{ $node->description }}</div>
            @endif
            @if($node->route)
                <div class="text-muted" style="font-size:0.7em;font-family:monospace;">{{ $node->route }}</div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="d-flex gap-1 flex-shrink-0" style="margin-top:4px;">
            <button class="btn btn-xs btn-outline-primary" style="font-size:0.75em;"
                    onclick='editSection({
                        id: {{ $node->id }},
                        title: {{ json_encode($node->title) }},
                        description: {{ json_encode($node->description ?? '') }},
                        icon: {{ json_encode($node->icon) }},
                        color: {{ json_encode($node->color) }},
                        route: {{ json_encode($node->route ?? '') }},
                        required_role: {{ json_encode($node->required_role ?? '') }},
                        required_area: {{ json_encode($node->required_area ?? '') }},
                        sort_order: {{ $node->sort_order }},
                        is_active: {{ $node->is_active ? 1 : 0 }},
                        parent_id: {{ $node->parent_id ?? 'null' }},
                        updateUrl: {{ json_encode(route('admin.sections.update', $node->id)) }}
                    })'>✏️</button>

            <form method="POST" action="{{ route('admin.sections.toggle', $node->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-xs {{ $node->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:0.75em;" title="{{ $node->is_active ? 'Disattiva' : 'Attiva' }}">
                    {{ $node->is_active ? '⏸' : '▶' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.sections.destroy', $node->id) }}" class="d-inline"
                  onsubmit="return confirm('Eliminare \"{{ $node->title }}\" e tutti i suoi figli?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.75em;">🗑</button>
            </form>
        </div>
    </div>

    {{-- Recursive children --}}
    @if($node->childrenRecursive->count() > 0)
        <div id="children-{{ $node->id }}">
            @foreach($node->childrenRecursive as $child)
                @include('admin.sections._node', ['node' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @endif
</div>

<script>
function toggleChildren(id) {
    const el = document.getElementById('children-' + id);
    if (el) el.style.display = el.style.display === 'none' ? '' : 'none';
}
</script>
