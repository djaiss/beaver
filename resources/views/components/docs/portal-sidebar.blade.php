@props(['navigation', 'locale', 'currentId'])

<aside class="doc-scroll sticky top-[7.5rem] hidden h-[calc(100vh-7.5rem)] w-[272px] shrink-0 self-start overflow-y-auto border-r border-gray-100 py-7 pr-4 pl-7 lg:block">
  @foreach ($navigation as $section)
    <div class="mb-5.5">
      <p class="mx-2 mb-1.5 text-[11px] font-semibold tracking-wider text-gray-400 uppercase">{{ $section['title'] }}</p>
      @foreach ($section['items'] as $item)
        @php($active = $item['id'] === $currentId)
        <a
          href="{{ $item['url'] }}"
          @class([
              'mb-px block rounded-r-md border-l-2 px-2.5 py-2 text-sm hover:bg-gray-50',
              'border-blue-600 bg-blue-50/60 font-semibold text-gray-900' => $active,
              'border-transparent font-medium text-gray-700' => ! $active,
          ])
        >{{ $item['title'] }}</a>
      @endforeach
    </div>
  @endforeach

  <div class="mt-2 border-t border-gray-100 pt-4">
    <a href="{{ route('marketing.index') }}" class="flex items-center gap-2 text-[13px] text-gray-400 hover:text-gray-700">
      <x-lucide-arrow-left class="h-3.5 w-3.5" />
      {{ __('Back to :name', ['name' => config('app.name')]) }}
    </a>
  </div>
</aside>
