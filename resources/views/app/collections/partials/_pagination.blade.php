@if ($items->hasPages())
    <nav class="mt-7 flex items-center justify-center gap-1.5" aria-label="{{ __('Pagination') }}">
        @if ($items->onFirstPage())
            <span class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted-soft opacity-50">‹</span>
        @else
            <a href="{{ $items->previousPageUrl() }}" rel="prev" class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted transition-colors hover:text-ink">‹</a>
        @endif

        @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
            @if ($page === $items->currentPage())
                <span class="flex size-9 items-center justify-center rounded-md bg-card text-sm font-medium text-ink">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="flex size-9 items-center justify-center rounded-md text-sm font-medium text-muted transition-colors hover:text-ink">{{ $page }}</a>
            @endif
        @endforeach

        @if ($items->hasMorePages())
            <a href="{{ $items->nextPageUrl() }}" rel="next" class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted transition-colors hover:text-ink">›</a>
        @else
            <span class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted-soft opacity-50">›</span>
        @endif
    </nav>
@endif
