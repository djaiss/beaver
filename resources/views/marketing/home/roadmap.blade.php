<section id="roadmap" class="mx-auto max-w-[1200px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24">
  <div class="mb-12">
    <p class="mb-3.5 text-[13px] font-semibold tracking-[0.6px] text-muted-soft uppercase">Roadmap</p>
    <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">Built in the open.</h2>
  </div>

  <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="rounded-lg bg-card p-8">
      <div class="mb-5 flex items-center gap-x-2.5">
        <span class="h-[9px] w-[9px] rounded-full bg-badge-emerald"></span>
        <p class="text-base font-semibold text-ink">Shipped</p>
      </div>
      <div class="flex flex-col">
        @foreach ($shipped as $entry)
          <div class="flex items-center gap-x-3 border-b border-hairline py-2.5 text-[15px] font-medium text-ink">
            <x-lucide-check class="h-4 w-4 shrink-0" stroke-width="2.4" />
            {{ $entry }}
          </div>
        @endforeach
      </div>
    </div>

    <div class="rounded-lg border border-hairline bg-canvas p-8">
      <div class="mb-5 flex items-center gap-x-2.5">
        <span class="h-[9px] w-[9px] rounded-full bg-warning"></span>
        <p class="text-base font-semibold text-ink">Coming soon</p>
      </div>
      <div class="flex flex-col">
        @foreach ($coming as $entry)
          <div class="flex items-center gap-x-3 border-b border-hairline-soft py-2.5 text-[15px] font-medium text-muted">
            <span class="h-4 w-4 shrink-0 rounded-full border-[1.5px] border-dashed border-hairline"></span>
            {{ $entry }}
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
