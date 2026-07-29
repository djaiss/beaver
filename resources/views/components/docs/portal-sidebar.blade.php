@props(['navigation', 'locale', 'currentId'])

<aside class="doc-scroll sticky top-[7.5rem] hidden h-[calc(100vh-7.5rem)] w-[272px] shrink-0 self-start overflow-y-auto border-r border-hairline-soft py-7 pr-4 pl-7 lg:block">
  @foreach ($navigation as $section)
    @php($sectionActive = collect($section['items'])->contains('id', $currentId))
    <div class="mb-5.5" x-data="{ open: {{ $sectionActive ? 'true' : 'false' }} }">
      <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        class="mb-1.5 flex w-full items-center gap-1 rounded px-2 py-1 text-left cursor-pointer"
      >
        <x-lucide-chevron-right class="h-2.5 w-2.5 shrink-0 text-muted-soft transition-transform duration-150" x-bind:class="{ 'rotate-90': open }" />
        <span class="text-[14px] text-body font-semibold">{{ $section['title'] }}</span>
      </button>
      <div x-show="open" x-cloak x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="-translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100">
        @foreach ($section['items'] as $item)
          @php($active = $item['id'] === $currentId)
          <a
            href="{{ $item['url'] }}"
            data-turbo="true"
            @class([
                'mb-px block rounded-r-md border-l-2 px-2.5 py-2 text-sm hover:bg-sidebar hover:border-brand hover:bg-brand/10',
                'border-brand bg-brand/10 text-ink' => $active,
                'border-transparent text-body' => ! $active,
            ])
          >{{ $item['title'] }}</a>
        @endforeach
      </div>
    </div>
  @endforeach

  <div class="mt-2 border-t border-hairline-soft pt-4">
    <a href="{{ route('marketing.index') }}" data-turbo="true" class="flex items-center gap-2 text-[13px] text-muted-soft hover:text-body">
      <x-lucide-arrow-left class="h-3.5 w-3.5" />
      {{ __('Back to :name', ['name' => config('app.name')]) }}
    </a>
  </div>
</aside>
