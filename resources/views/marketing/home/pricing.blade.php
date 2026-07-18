<section id="pricing" class="mx-auto max-w-[1200px] scroll-mt-24 px-5 pt-16 sm:px-8 sm:pt-24">
  <div class="mb-8 text-center">
    <div class="mb-5 inline-flex items-center gap-x-2 rounded-full bg-card py-1.5 pr-3.5 pl-1.5 text-[13px] font-semibold text-ink">
      <span class="rounded-full bg-primary px-2 py-[3px] text-[11px] text-on-primary">NO SUBSCRIPTION</span>
      Pay once. Own it forever.
    </div>
    <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">One price. No subscriptions.</h2>
    <p class="mx-auto mt-4.5 max-w-[520px] text-[17px] text-muted">
      Self-host it for free, or buy the managed cloud once. A single payment, no monthly bill, no renewal, ever. Your data is portable either way.
    </p>
  </div>

  <div class="mx-auto grid max-w-[840px] grid-cols-1 gap-6 md:grid-cols-2">
    <div class="flex flex-col rounded-lg border border-hairline bg-canvas p-8">
      <p class="text-[22px] font-semibold tracking-[-0.3px] text-ink">Self-host</p>
      <p class="mt-3 text-[28px] font-semibold tracking-[-0.5px] text-ink">Free<span class="text-[15px] font-medium text-muted"> &middot; forever</span></p>

      <div class="my-6 flex flex-col gap-y-3">
        @foreach ($selfHostFeatures as $feature)
          <div class="flex items-center gap-x-2.5 text-[15px] text-body">
            <x-lucide-check class="h-4 w-4 shrink-0 text-ink" stroke-width="2.4" />
            {{ $feature }}
          </div>
        @endforeach
      </div>

      <div class="flex-1"></div>
      <a href="{{ route('marketing.docs.api.index') }}" class="flex h-11 items-center justify-center rounded-md border border-hairline bg-canvas text-sm font-semibold text-ink transition-colors hover:bg-sidebar">Read the docs</a>
    </div>

    <div class="flex flex-col rounded-lg bg-[#101010] p-8 text-white">
      <div class="flex items-center gap-x-2.5">
        <p class="text-[22px] font-semibold tracking-[-0.3px]">Cloud</p>
        <span class="rounded-full bg-[#1a1a1a] px-2 py-[3px] text-[11px] font-semibold text-badge-emerald">PAY ONCE</span>
      </div>
      <div class="mt-3 flex flex-wrap items-baseline gap-x-2">
        <p class="text-[28px] font-semibold tracking-[-0.5px]">$49<span class="text-[15px] font-medium text-[#a1a1aa]"> once</span></p>
        <span class="text-[13px] text-[#6b7280] line-through">$6/mo forever</span>
      </div>

      <div class="my-6 flex flex-col gap-y-3">
        @foreach ($cloudFeatures as $feature)
          <div class="flex items-center gap-x-2.5 text-[15px] text-[#e5e7eb]">
            <x-lucide-check class="h-4 w-4 shrink-0 text-badge-emerald" stroke-width="2.4" />
            {{ $feature }}
          </div>
        @endforeach
      </div>

      <div class="flex-1"></div>
      @auth
        <a href="{{ route('dashboard.index') }}" class="flex h-11 items-center justify-center rounded-md bg-white text-sm font-semibold text-[#111111] transition-colors hover:bg-[#e5e7eb]">Go to your account</a>
      @else
        <a href="{{ route('register') }}" class="flex h-11 items-center justify-center rounded-md bg-white text-sm font-semibold text-[#111111] transition-colors hover:bg-[#e5e7eb]">Buy once, $49</a>
      @endauth
    </div>
  </div>
</section>
