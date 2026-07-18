<section class="mx-auto max-w-[1200px] px-5 pt-16 text-center sm:px-8 sm:pt-24">
  <h2 class="text-[28px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-4xl lg:text-5xl lg:tracking-[-1.5px]">One app for every collection.</h2>
  <p class="mx-auto mt-4.5 max-w-[520px] text-[17px] text-muted">
    Not just books. Not just comics. Not just wine. If you collect it, {{ config('app.name') }} catalogs it.
  </p>

  <div class="mt-12 grid grid-cols-2 gap-4 text-left sm:grid-cols-3 lg:grid-cols-5">
    @foreach ($supported as $collection)
      <div class="overflow-hidden rounded-lg border border-hairline transition-shadow hover:shadow-[0_4px_12px_rgba(0,0,0,0.06)]">
        <div class="h-18" style="background: repeating-linear-gradient(135deg, {{ $collection['from'] }} 0px, {{ $collection['from'] }} 10px, {{ $collection['to'] }} 10px, {{ $collection['to'] }} 20px)"></div>
        <p class="px-3.5 py-3 text-sm font-semibold text-ink">{{ $collection['name'] }}</p>
      </div>
    @endforeach

    <div class="flex flex-col items-center justify-center gap-y-1.5 rounded-lg border border-dashed border-hairline bg-sidebar p-4">
      <span class="text-[22px] font-semibold tracking-[-0.5px] text-ink">+</span>
      <p class="text-center text-[13px] font-semibold text-ink">…and anything else</p>
      <p class="text-center text-xs text-muted-soft">Create your own type</p>
    </div>
  </div>
</section>
