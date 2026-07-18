<section class="mx-auto max-w-[1200px] px-5 pt-16 sm:px-8 sm:pt-24">
  <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-12">
    <div class="overflow-hidden rounded-lg border border-hairline">
      <div class="flex items-center justify-between gap-3 border-b border-hairline-soft px-5 py-4">
        <p class="text-[15px] font-semibold text-ink">Amazing Spider-Man #1</p>
        <span class="shrink-0 rounded-full bg-card px-2.5 py-1 text-xs font-medium text-body">3 copies owned</span>
      </div>

      @foreach ($copies as $copy)
        <div class="flex items-center gap-x-3.5 border-b border-hairline-soft px-5 py-3.5">
          <span class="h-13 w-10 shrink-0 rounded-sm" style="background: repeating-linear-gradient(135deg, {{ $copy['from'] }} 0px, {{ $copy['from'] }} 7px, {{ $copy['to'] }} 7px, {{ $copy['to'] }} 14px)"></span>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-ink">{{ $copy['condition'] }}</p>
            <p class="mt-0.5 truncate text-xs text-muted-soft">{{ $copy['location'] }} &middot; added {{ $copy['added'] }}</p>
          </div>
          <div class="shrink-0 text-right">
            <p class="text-sm font-semibold text-ink">{{ $copy['value'] }}</p>
            <p class="text-xs text-muted-soft">paid {{ $copy['paid'] }}</p>
          </div>
        </div>
      @endforeach
    </div>

    <div>
      <p class="mb-3.5 text-[13px] font-semibold tracking-[0.6px] text-muted-soft uppercase">Track physical copies</p>
      <h2 class="text-[28px] leading-[1.15] font-semibold tracking-[-1px] text-ink sm:text-4xl">One catalog entry. Every copy you own.</h2>
      <p class="mt-4.5 mb-6 text-base leading-relaxed text-muted">
        Some things you own more than once. Track each copy independently: purchase date, price paid, estimated value, condition, location, and provenance.
      </p>

      <div class="flex flex-wrap gap-2.5">
        @foreach ($copyFields as $field)
          <span class="rounded-full bg-card px-3.5 py-1.5 text-sm font-medium text-body">{{ $field }}</span>
        @endforeach
      </div>
    </div>
  </div>
</section>
