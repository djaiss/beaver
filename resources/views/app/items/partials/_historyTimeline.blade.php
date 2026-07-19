{{--
  The timeline: everything that has happened to the copy, oldest first, drawn
  from the sections that carry real records. Only the valuations do so far, so
  the timeline reads as the valuations until the other sections land.
--}}

<div class="mb-4">
  <p class="text-lg font-semibold text-ink">{{ __('Timeline') }}</p>
  <p class="mt-1 text-[13px] leading-relaxed text-muted">{{ __('Everything that has happened to this copy, oldest first. The sections listed alongside are what it is assembled from.') }}</p>
</div>

@forelse ($selectedCopy->valuations->sortBy('valued_at') as $valuation)
  <div class="flex items-start gap-3 border-b border-hairline-soft py-3.5 last:border-b-0" data-test="history-valuation-{{ $valuation->id }}">
    <span class="mt-1.5 size-2 shrink-0 rounded-sm bg-[#3b82f6]"></span>

    <div class="min-w-0 flex-1">
      <p class="text-sm font-semibold text-ink">{{ __('Valued at :amount', ['amount' => $money($valuation->amount)]) }}</p>
      <p class="text-xs text-muted-soft">{{ $valuation->type->label() }}</p>
    </div>

    <span class="shrink-0 font-mono text-xs text-muted-soft">{{ $valuation->valued_at->isoFormat('MMM YYYY') }}</span>
  </div>
@empty
  <div class="rounded-xl border border-hairline">
    <x-empty-state data-test="no-history">
      <x-slot:icon>
        <x-lucide-clock class="size-6 text-muted" />
      </x-slot>

      {{ __('Nothing has been recorded against this copy yet.') }}
    </x-empty-state>
  </div>
@endforelse
