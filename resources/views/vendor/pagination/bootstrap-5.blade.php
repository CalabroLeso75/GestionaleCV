@if ($paginator->hasPages())
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3 py-2 px-3 bg-light rounded border">

    {{-- Riepilogo risultati --}}
    <div class="text-muted small">
        @if ($paginator->firstItem())
            <strong>{{ number_format($paginator->firstItem()) }}</strong>–<strong>{{ number_format($paginator->lastItem()) }}</strong>
            di <strong>{{ number_format($paginator->total()) }}</strong>
        @else
            Nessun risultato
        @endif
    </div>

    {{-- Navigazione compatta: << < [pagina] > >> --}}
    <form method="GET" action="" id="paginationJumpForm" class="d-flex align-items-center gap-1">
        {{-- Mantieni tutti i parametri correnti --}}
        @foreach(request()->except(['page']) as $key => $val)
            @if(is_array($val))
                @foreach($val as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach

        {{-- Prima pagina --}}
        @if ($paginator->onFirstPage())
            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Prima pagina">«</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Pagina precedente">‹</button>
        @else
            <a href="{{ $paginator->url(1) }}" class="btn btn-sm btn-outline-primary" title="Prima pagina">«</a>
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-sm btn-outline-primary" title="Pagina precedente">‹</a>
        @endif

        {{-- Input pagina --}}
        <div class="input-group input-group-sm" style="width: auto;">
            <span class="input-group-text text-muted small">Pag.</span>
            <input type="number" name="page" class="form-control form-control-sm text-center"
                   value="{{ $paginator->currentPage() }}"
                   min="1" max="{{ $paginator->lastPage() }}"
                   style="width: 60px;"
                   onchange="this.form.submit()"
                   title="Vai alla pagina">
            <span class="input-group-text text-muted small">/ {{ $paginator->lastPage() }}</span>
        </div>

        {{-- Pagina successiva / ultima --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-sm btn-outline-primary" title="Pagina successiva">›</a>
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="btn btn-sm btn-outline-primary" title="Ultima pagina">»</a>
        @else
            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Pagina successiva">›</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Ultima pagina">»</button>
        @endif
    </form>

    {{-- Per-page selector --}}
    <form method="GET" action="" class="d-flex align-items-center gap-1" id="perPageForm">
        @foreach(request()->except(['page', 'per_page']) as $key => $val)
            @if(is_array($val))
                @foreach($val as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach
        <label class="text-muted small mb-0 me-1">Righe per pag.</label>
        <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
            @foreach([30, 50, 100, 200] as $size)
                <option value="{{ $size }}" {{ request('per_page', 30) == $size ? 'selected' : '' }}>{{ $size }}</option>
            @endforeach
        </select>
    </form>

</div>
@endif
