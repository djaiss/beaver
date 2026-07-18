<section id="features" class="mx-auto max-w-[1200px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24">
  <div class="mb-12 max-w-[640px]">
    <p class="mb-3.5 text-[13px] font-semibold tracking-[0.6px] text-muted-soft uppercase">Organize everything</p>
    <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">Structure that bends to how you collect.</h2>
    <p class="mt-5 text-[17px] leading-relaxed text-muted">
      Unlimited collections, nested categories, tags, locations, and conditions, all on beautiful item pages that make your catalog feel like a museum, not a spreadsheet.
    </p>
  </div>

  <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    @foreach ($organizeFeatures as $feature)
      <div class="flex flex-col gap-y-3 rounded-lg border border-hairline bg-canvas p-6 transition-shadow hover:shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
        <span class="flex h-[34px] w-[34px] items-center justify-center rounded-md bg-card">
          <span class="h-3.5 w-3.5 rounded-sm" style="background-color: {{ $feature['dot'] }}"></span>
        </span>
        <p class="text-base font-semibold text-ink">{{ $feature['title'] }}</p>
        <p class="text-sm leading-relaxed text-muted">{{ $feature['description'] }}</p>
      </div>
    @endforeach
  </div>
</section>
