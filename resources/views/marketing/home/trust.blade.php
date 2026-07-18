<section class="mx-auto max-w-[1200px] px-5 pt-16 sm:px-8 sm:pt-24">
  <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    @foreach ($trustCards as $card)
      <div class="flex flex-col gap-y-5 rounded-lg bg-card p-8">
        <span class="flex h-10 w-10 items-center justify-center rounded-[10px] bg-primary">
          <x-dynamic-component :component="'lucide-' . $card['icon']" class="h-5 w-5 text-on-primary" />
        </span>
        <p class="text-[22px] font-semibold tracking-[-0.3px] text-ink">{{ $card['title'] }}</p>
        <div class="flex flex-wrap gap-2">
          @foreach ($card['items'] as $item)
            <span class="rounded-full border border-hairline bg-canvas px-3 py-[5px] text-sm font-medium text-body">{{ $item }}</span>
          @endforeach
        </div>
        <p class="mt-0.5 text-[15px] leading-relaxed text-muted">{{ $card['description'] }}</p>
      </div>
    @endforeach
  </div>
</section>
