{{-- The By party tab: one card per person or institution, most active first. --}}
@include('app.loans.partials._filterBar', ['withStatusSort' => false])

@php($parties = $tabData['parties'])

@forelse ($parties as $party)
  <div class="mb-4 rounded-xl border border-hairline bg-canvas">
    <div class="flex items-center gap-3 border-b border-hairline px-4 py-3">
      <x-avatar-initials :name="$party['name']" class="size-9 text-xs" />
      <div class="min-w-0 flex-1">
        <div class="truncate text-sm font-semibold text-ink">{{ $party['name'] }}</div>
        <div class="text-[12px] text-muted">{{ trans_choice(':count loan total|:count loans total', $party['loans']->count(), ['count' => $party['loans']->count()]) }}</div>
      </div>
      @if ($party['active'] > 0)
        <span class="rounded-full bg-badge-emerald/15 px-2.5 py-0.5 text-xs font-medium text-badge-emerald">{{ __(':count active', ['count' => $party['active']]) }}</span>
      @endif
    </div>

    @foreach ($party['loans'] as $loan)
      @include('app.loans.partials._loanRow', ['loan' => $loan, 'direction' => $direction, 'tab' => $tab, 'compact' => true])
      @if (! $loop->last)
        <div class="border-b border-hairline"></div>
      @endif
    @endforeach
  </div>
@empty
  <x-empty-state data-test="no-parties">
    <x-slot:icon>
      <x-lucide-users class="size-6 text-muted" />
    </x-slot>
    {{ __('No loans match these filters.') }}
  </x-empty-state>
@endforelse
