@if ($photos->hasPages())
  @php
    // An account can run to hundreds of pages, so only a window around the current
    // page is listed, with the first and last always reachable.
    $last = $photos->lastPage();
    $current = $photos->currentPage();
    $window = range(max(1, $current - 2), min($last, $current + 2));
    $pages = array_values(array_unique(array_merge([1], $window, [$last])));
    sort($pages);
  @endphp

  <nav class="mt-7 flex items-center justify-center gap-1.5" aria-label="{{ __('Pagination') }}">
    @if ($photos->onFirstPage())
      <span class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted-soft opacity-50">‹</span>
    @else
      <a href="{{ $photos->previousPageUrl() }}" rel="prev" data-turbo="true" class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted transition-colors hover:text-ink">‹</a>
    @endif

    @php $previous = 0; @endphp
    @foreach ($pages as $page)
      @if ($page - $previous > 1)
        <span class="flex size-9 items-center justify-center text-sm text-muted-soft">…</span>
      @endif

      @if ($page === $current)
        <span class="flex size-9 items-center justify-center rounded-md bg-card text-sm font-medium text-ink" aria-current="page">{{ $page }}</span>
      @else
        <a href="{{ $photos->url($page) }}" data-turbo="true" class="flex size-9 items-center justify-center rounded-md text-sm font-medium text-muted transition-colors hover:text-ink">{{ $page }}</a>
      @endif

      @php $previous = $page; @endphp
    @endforeach

    @if ($photos->hasMorePages())
      <a href="{{ $photos->nextPageUrl() }}" rel="next" data-turbo="true" class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted transition-colors hover:text-ink">›</a>
    @else
      <span class="flex size-9 items-center justify-center rounded-md border border-hairline text-muted-soft opacity-50">›</span>
    @endif
  </nav>
@endif
