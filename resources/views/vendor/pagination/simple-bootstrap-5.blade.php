@if ($paginator->hasPages())
<div class="d-flex align-items-center justify-content-between mt-2">
    <div class="text-muted small">
        @if($paginator->onFirstPage())
            <span class="text-muted">« Prima</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm btn-outline-primary">« Precedente</a>
        @endif
    </div>
    <div class="text-muted small">Pagina {{ $paginator->currentPage() }}</div>
    <div>
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm btn-outline-primary">Successiva »</a>
        @else
            <span class="text-muted">Successiva »</span>
        @endif
    </div>
</div>
@endif
