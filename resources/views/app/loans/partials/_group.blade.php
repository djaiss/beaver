{{--
  A titled group of loans: a coloured dot, a heading, a one-line description, a
  count, and the rows, with a friendly line when the group is empty. Shared by the
  Due & overdue and Risk & exceptions tabs.

  Expects: $title, $description, $dot, $loans, $emptyMessage, $direction, $tab.
--}}
<div class="rounded-xl border border-hairline bg-canvas">
  <div class="flex items-center gap-2 border-b border-hairline px-4 py-3">
    <span class="size-2 rounded-full {{ $dot }}"></span>
    <span class="text-sm font-semibold text-ink">{{ $title }}</span>
    <span class="text-xs text-muted">· {{ $description }}</span>
    <span class="ml-auto text-xs font-medium text-muted-soft">{{ number_format($loans->count()) }}</span>
  </div>

  @forelse ($loans as $loan)
    @include('app.loans.partials._loanRow', ['loan' => $loan, 'direction' => $direction, 'tab' => $tab, 'compact' => true])
    @if (! $loop->last)
      <div class="border-b border-hairline"></div>
    @endif
  @empty
    <p class="px-4 py-4 text-[13px] text-muted-soft">{{ $emptyMessage }}</p>
  @endforelse
</div>
