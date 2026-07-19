{{--
  The valuations of the copy, most recent first, which is the order the copy
  reads its current worth from. Valuations are append-only, so this is a record
  of what the copy has been reckoned to be worth rather than a single figure.
--}}

<div class="mb-4">
  <p class="text-lg font-semibold text-ink">{{ __('Valuations') }}</p>
  <p class="mt-1 text-[13px] leading-relaxed text-muted">{{ __('What the copy has been reckoned to be worth, over time. The most recent is its current estimated value.') }}</p>
</div>

@forelse ($selectedCopy->valuations as $valuation)
  <div class="flex items-center justify-between gap-4 border-b border-hairline-soft py-3.5 last:border-b-0" data-test="history-valuation-{{ $valuation->id }}">
    <div class="min-w-0">
      <p class="text-sm font-semibold text-ink">{{ $money($valuation->amount) }}</p>
      <p class="text-xs text-muted-soft">{{ $valuation->type->label() }}</p>
    </div>

    <span class="shrink-0 font-mono text-xs text-muted-soft">{{ $valuation->valued_at->isoFormat('MMM D, YYYY') }}</span>
  </div>
@empty
  <div class="rounded-xl border border-hairline">
    <x-empty-state data-test="no-valuations">
      <x-slot:icon>
        <x-lucide-clock class="size-6 text-muted" />
      </x-slot>

      {{ __('No valuation has been recorded for this copy yet.') }}
    </x-empty-state>
  </div>
@endforelse
