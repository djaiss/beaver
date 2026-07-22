<x-marketing-layout :title="$feature['title']">
  <section class="mx-auto max-w-[720px] px-5 pt-16 pb-24 sm:px-8 sm:pt-24">
    <a href="{{ route('marketing.features.index') }}" data-turbo="true" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-body transition-colors hover:text-ink">
      @svg('lucide-arrow-left', 'size-3.5')
      {{ __('All features') }}
    </a>

    <div class="mt-8 flex items-center gap-3">
      <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-card">
        <span class="h-5 w-5" style="border-radius:{{ $feature['iconRadius'] }}; background:{{ $feature['dot'] }};"></span>
      </span>
      @if ($feature['isNew'])
        <span class="rounded-full bg-[#e7f6ee] px-2 py-0.5 text-[10px] font-bold tracking-[0.4px] text-[#0f7a4d]">{{ __('NEW') }}</span>
      @endif
    </div>

    <h1 class="mt-6 text-[32px] leading-[1.1] font-semibold tracking-[-1px] text-ink sm:text-[40px]">{{ $feature['title'] }}</h1>
    <p class="mt-4 text-[18px] leading-[1.5] text-muted">{{ $feature['desc'] }}</p>

    <div class="mt-10 rounded-xl border border-dashed border-hairline bg-card px-6 py-10 text-center">
      <p class="text-[15px] font-semibold text-ink">{{ __('This page is on its way.') }}</p>
      <p class="mx-auto mt-2 max-w-[420px] text-[14px] text-muted">{{ __('We are still writing the full story for this feature. In the meantime, browse the rest of what :name can do.', ['name' => config('app.name')]) }}</p>
      <a href="{{ route('marketing.features.index') }}" data-turbo="true" class="mt-6 inline-flex h-11 items-center gap-x-2 rounded-md bg-primary px-5 text-[14px] font-semibold text-on-primary transition-opacity hover:opacity-90">
        {{ __('Explore all features') }}
        @svg('lucide-arrow-right', 'size-4')
      </a>
    </div>
  </section>
</x-marketing-layout>
