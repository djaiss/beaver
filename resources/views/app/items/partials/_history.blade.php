{{--
  The chronological view across everything hanging off a copy.

  Almost none of what this tab will read from exists yet, so the sections are
  listed with what they are for rather than hidden until they are built: the
  shape of the screen is the point, and an empty section says more about what is
  coming than no section at all. Valuations are the one history the copy
  restructuring brought with it, so they are the only ones filled in.
--}}

@php
  $sections = [
      ['key' => 'timeline', 'label' => __('Timeline'), 'blurb' => __('Everything that has happened to this copy, oldest first.'), 'ready' => true],
      ['key' => 'transactions', 'label' => __('Transactions'), 'blurb' => __('Financial and ownership exchanges. The source of truth for prices, fees and totals.'), 'ready' => false],
      ['key' => 'provenance', 'label' => __('Provenance'), 'blurb' => __('Meaningful events in ownership, custody, origin and authenticity. No financial data lives here.'), 'ready' => false],
      ['key' => 'valuations', 'label' => __('Valuations'), 'blurb' => __('What the copy has been reckoned to be worth, over time.'), 'ready' => true],
      ['key' => 'insurance', 'label' => __('Insurance'), 'blurb' => __('Coverage, and how the insured value has moved.'), 'ready' => false],
      ['key' => 'maintenance', 'label' => __('Maintenance'), 'blurb' => __('Cleaning, repair, servicing and restoration performed on the copy.'), 'ready' => false],
      ['key' => 'loans', 'label' => __('Loans'), 'blurb' => __('Custody out and back, without ownership changing.'), 'ready' => false],
      ['key' => 'locations', 'label' => __('Locations'), 'blurb' => __('Where the copy has been kept.'), 'ready' => false],
      ['key' => 'documents', 'label' => __('Documents'), 'blurb' => __('Files attached to the copy or to anything hanging off it.'), 'ready' => false],
  ];
@endphp

<div class="flex flex-col gap-6">
  @forelse ($item->copies as $copy)
    <div class="overflow-hidden rounded-xl border border-hairline" data-test="history-copy-{{ $copy->id }}">
      <div class="flex flex-wrap items-center gap-2.5 border-b border-hairline px-5 py-4">
        <p class="text-[15px] font-semibold text-ink">{{ __('Copy :number', ['number' => $loop->iteration]) }}</p>

        @if ($copy->identifier)
          <span class="rounded-md bg-card px-2 py-0.5 font-mono text-xs text-muted">{{ $copy->identifier }}</span>
        @endif

        <x-badge :color="$copy->status->color()">{{ $copy->status->label() }}</x-badge>
      </div>

      <div class="grid grid-cols-1 gap-6 p-5 lg:grid-cols-[190px_1fr]">
        {{-- The sections this history is assembled from. --}}
        <div class="flex flex-col gap-0.5">
          @foreach ($sections as $section)
            <div class="flex items-center justify-between gap-2 rounded-md px-2.5 py-2">
              <span class="text-[13px] font-medium {{ $section['ready'] ? 'text-ink' : 'text-muted-soft' }}">{{ $section['label'] }}</span>

              @unless ($section['ready'])
                <x-soon />
              @endunless
            </div>
          @endforeach
        </div>

        <div class="min-w-0">
          <p class="mb-1 text-[15px] font-semibold text-ink">{{ __('Timeline') }}</p>
          <p class="mb-4 text-[13px] leading-relaxed text-muted">{{ __('Everything that has happened to this copy, oldest first. The sections listed alongside are what it will be assembled from.') }}</p>

          @forelse ($copy->valuations->sortBy('valued_at') as $valuation)
            <div class="flex items-start gap-3 border-b border-hairline-soft py-3 last:border-b-0" data-test="history-valuation-{{ $valuation->id }}">
              <span class="mt-1.5 size-2 shrink-0 rounded-full bg-badge-violet"></span>

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
        </div>
      </div>
    </div>
  @empty
    <div class="rounded-xl border border-hairline">
      <x-empty-state data-test="no-copies-to-track">
        <x-slot:icon>
          <x-lucide-clock class="size-6 text-muted" />
        </x-slot>

        {{ __('This item has no copies, so there is nothing to track the history of.') }}
      </x-empty-state>
    </div>
  @endforelse
</div>
