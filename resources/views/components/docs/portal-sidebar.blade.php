@props(['navigation', 'locale', 'currentId'])

<aside class="doc-scroll sticky top-[7.5rem] hidden h-[calc(100vh-7.5rem)] w-[272px] shrink-0 self-start overflow-y-auto border-r border-gray-100 py-7 pr-4 pl-7 lg:block">
  @foreach ($navigation as $section)
    @php($sectionActive = collect($section['items'])->contains('id', $currentId))
    <div class="mb-5.5" x-data="{ open: {{ $sectionActive ? 'true' : 'false' }} }">
      <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        class="mb-1.5 flex w-full items-center gap-1 rounded px-2 py-1 text-left cursor-pointer"
      >
        <x-lucide-chevron-right class="h-2.5 w-2.5 shrink-0 text-gray-400 transition-transform duration-150" x-bind:class="{ 'rotate-90': open }" />
        <span class="text-[14px] text-gray-700 font-semibold">{{ $section['title'] }}</span>
      </button>
      <div x-show="open" x-cloak x-transition:enter="transition duration-150 ease-out" x-transition:enter-start="-translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100">
        @foreach ($section['items'] as $item)
          @php($active = $item['id'] === $currentId)
          <a
            href="{{ $item['url'] }}"
            @class([
                'mb-px block rounded-r-md border-l-2 px-2.5 py-2 text-sm hover:bg-gray-50 hover:border-blue-600 hover:bg-blue-50/60',
                'border-blue-600 bg-blue-50/60 text-gray-900' => $active,
                'border-transparent text-gray-700' => ! $active,
            ])
          >{{ $item['title'] }}</a>
        @endforeach
      </div>
    </div>
  @endforeach

  <div class="mt-2 border-t border-gray-100 pt-4">
    <a href="{{ route('marketing.index') }}" class="flex items-center gap-2 text-[13px] text-gray-400 hover:text-gray-700">
      <x-lucide-arrow-left class="h-3.5 w-3.5" />
      {{ __('Back to :name', ['name' => config('app.name')]) }}
    </a>
  </div>
</aside>
