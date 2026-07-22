<x-marketing-layout :title="__('Features')">
  <section class="mx-auto max-w-[1200px] px-5 pt-16 sm:px-8 sm:pt-24">
    <div class="mx-auto max-w-[640px] text-center">
      <p class="mb-3.5 text-[13px] font-semibold tracking-[0.6px] text-muted-soft uppercase">{{ __('Everything :name does', ['name' => config('app.name')]) }}</p>
      <h1 class="text-[32px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-5xl lg:tracking-[-1.5px]">{{ __('Everything you collect, understood in full.') }}</h1>
      <p class="mx-auto mt-4.5 max-w-[540px] text-[17px] text-muted">{{ __('Catalogue what you own, track each physical copy, preserve its history, see its value, and keep control of the whole thing.') }}</p>
    </div>

    <div class="mt-16 grid gap-8 sm:mt-20 lg:grid-cols-3">
      @foreach ($columns as $column)
        <div>
          <div class="mb-4 flex items-center gap-2 border-b border-hairline-soft pb-3">
            <span class="h-1.5 w-1.5 rounded-full" style="background:{{ $column['dot'] }};"></span>
            <span class="text-[11px] font-semibold tracking-[0.6px] text-muted-soft uppercase">{{ $column['label'] }}</span>
          </div>

          <div class="flex flex-col gap-2.5">
            @foreach ($column['items'] as $item)
              <a href="{{ route('marketing.features.show', $item['slug']) }}" data-turbo="true" class="group flex items-start gap-3.5 rounded-xl border border-hairline bg-canvas p-4 transition-colors hover:bg-sidebar">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-card">
                  <span class="h-3.5 w-3.5" style="border-radius:{{ $item['iconRadius'] }}; background:{{ $item['dot'] }};"></span>
                </span>
                <span class="min-w-0">
                  <span class="flex items-center gap-2">
                    <span class="text-[15px] font-semibold text-ink">{{ $item['title'] }}</span>
                    @if ($item['isNew'])
                      <span class="rounded-full bg-[#e7f6ee] px-1.5 py-0.5 text-[9px] font-bold tracking-[0.4px] text-[#0f7a4d]">{{ __('NEW') }}</span>
                    @endif
                  </span>
                  <span class="mt-1 block text-[13px] leading-[1.5] text-pretty text-muted">{{ $item['desc'] }}</span>
                </span>
              </a>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </section>

  <section class="mx-auto max-w-[1200px] px-5 pt-16 pb-8 text-center sm:px-8 sm:pt-24">
    <a href="{{ route('register') }}" data-turbo="true" class="inline-flex h-12 items-center gap-x-2 rounded-md bg-primary px-6 text-[15px] font-semibold text-on-primary transition-opacity hover:opacity-90">
      {{ __('Get started') }}
      @svg('lucide-arrow-right', 'size-4')
    </a>
  </section>
</x-marketing-layout>
