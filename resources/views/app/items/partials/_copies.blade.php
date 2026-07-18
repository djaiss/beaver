<div class="flex max-w-3xl flex-col gap-4">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-muted">
      {{ trans_choice(':count physical copy|:count physical copies', $item->copies->count(), ['count' => $item->copies->count()]) }}
      @if ($totalEstimated > 0)
        · {{ __('total est. value :value', ['value' => $money($totalEstimated)]) }}
      @endif
    </p>

    <span class="flex h-9 cursor-not-allowed items-center gap-2 rounded-md border border-hairline px-3.5 text-[13px] font-semibold text-muted-soft">
      {{ __('Add copy') }}
      <x-soon />
    </span>
  </div>

  @forelse ($item->copies as $copy)
    <div class="overflow-hidden rounded-xl border border-hairline">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-hairline px-5 py-4">
        <div class="flex items-center gap-3">
          <p class="text-[15px] font-semibold text-ink">{{ __('Copy :number', ['number' => $loop->iteration]) }}</p>
          <x-badge>{{ $copy->condition?->name ?? __('No condition') }}</x-badge>
        </div>

        <p class="text-base font-semibold text-ink">{{ $copy->estimated_value ? $money((int) $copy->estimated_value) : '—' }}</p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-4">
        @php
          $facts = [
              __('Condition') => $copy->condition?->name ?? '—',
              __('Location') => $copy->location?->name ?? '—',
              __('Acquired') => $copy->acquired_at?->isoFormat('MMM YYYY') ?? '—',
              __('Price paid') => $copy->price_paid ? $money((int) $copy->price_paid) : '—',
          ];
        @endphp

        @foreach ($facts as $label => $value)
          <div class="border-b border-hairline px-5 py-3.5 last:border-r-0 sm:border-b-0 sm:border-r sm:border-r-hairline">
            <p class="mb-1 text-xs text-muted-soft">{{ $label }}</p>
            <p class="truncate text-sm font-semibold text-ink">{{ $value }}</p>
          </div>
        @endforeach
      </div>

      {{-- Where a copy came from is not recorded anywhere yet. --}}
      <div class="border-t border-hairline bg-card px-5 py-4">
        <div class="flex items-center gap-2">
          <p class="text-xs font-semibold tracking-wide text-muted-soft uppercase">{{ __('Provenance') }}</p>
          <x-soon />
        </div>

        <p class="mt-2 text-[13px] text-muted-soft">{{ __('A copy does not record where it came from yet, so there is no history to show.') }}</p>
      </div>
    </div>
  @empty
    <div class="rounded-xl border border-hairline">
      <x-empty-state data-test="no-copies">
        <x-slot:icon>
          <x-lucide-package class="size-6 text-muted" />
        </x-slot>

        {{ __('No copies of this item yet.') }}
      </x-empty-state>
    </div>
  @endforelse
</div>
