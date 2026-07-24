{{-- The All loans tab: every loan in this direction, filtered and sorted. --}}
@include('app.loans.partials._filterBar', ['withStatusSort' => true])

@php($loans = $tabData['loans'])

<div class="overflow-hidden rounded-xl border border-hairline bg-canvas">
  @forelse ($loans as $loan)
    @include('app.loans.partials._loanRow', ['loan' => $loan, 'direction' => $direction, 'tab' => $tab])
    @if (! $loop->last)
      <div class="border-b border-hairline"></div>
    @endif
  @empty
    <x-empty-state data-test="no-loans">
      <x-slot:icon>
        <x-lucide-arrow-left-right class="size-6 text-muted" />
      </x-slot>
      {{ __('No loans match these filters.') }}
    </x-empty-state>
  @endforelse
</div>
