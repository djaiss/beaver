@php
    // Everything an item will eventually track. Listing them here is how the
    // screen shows what is still missing.
    $modules = [
        ['color' => 'bg-badge-emerald', 'title' => __('Purchase & sale history'), 'description' => __('Every transaction for this item across all copies, with prices and counterparties.')],
        ['color' => 'bg-brand', 'title' => __('Valuation over time'), 'description' => __('Track estimated value as it moves, charted against what you paid.')],
        ['color' => 'bg-badge-violet', 'title' => __('Insurance'), 'description' => __('Policy coverage, insured value, and scheduled item documentation.')],
        ['color' => 'bg-warning', 'title' => __('Maintenance & restoration'), 'description' => __('Log cleaning and restoration work performed on each copy.')],
        ['color' => 'bg-badge-pink', 'title' => __('Loans'), 'description' => __('Who currently has the item, when it went out, and when it is due back.')],
        ['color' => 'bg-success', 'title' => __('Storage history'), 'description' => __('A full trail of every location this item has moved between over time.')],
        ['color' => 'bg-badge-violet', 'title' => __('Documents & receipts'), 'description' => __('Attach certificates, appraisals, and receipts in one place per item.')],
        ['color' => 'bg-badge-orange', 'title' => __('Related items'), 'description' => __('Link variants, reprints, and companion issues into a connected graph.')],
    ];
@endphp

<div class="max-w-4xl">
  <p class="mb-6 max-w-xl text-[15px] leading-relaxed text-muted">{{ __('Everything an item will eventually track. None of these modules are built yet, and the item model is meant to grow into each of them.') }}</p>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    @foreach ($modules as $module)
      <div class="flex items-start gap-3.5 rounded-xl border border-hairline p-5">
        <span class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg bg-card">
          <span class="size-3.5 rounded {{ $module['color'] }}"></span>
        </span>

        <div class="min-w-0">
          <div class="mb-1 flex flex-wrap items-center gap-2">
            <p class="text-[15px] font-semibold text-ink">{{ $module['title'] }}</p>
            <x-soon />
          </div>

          <p class="text-[13px] leading-relaxed text-muted">{{ $module['description'] }}</p>
        </div>
      </div>
    @endforeach
  </div>
</div>
