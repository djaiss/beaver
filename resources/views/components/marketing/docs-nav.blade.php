@props ([
  'items' => [],
  'version' => '',
  'depth' => 0,
])

@foreach ($items as $item)
  @if (count($item['children']) > 0)
    <p class="{{ $loop->first && $depth === 0 ? '' : 'mt-4' }} mb-1 border-b border-gray-200 pb-2 font-mono text-xs font-bold tracking-wider text-gray-600 uppercase dark:border-gray-700 dark:text-gray-300" style="margin-left: {{ $depth * 18 }}px">{{ $item['label'] }}</p>
    <div class="mb-3">
      <x-marketing.docs-nav :items="$item['children']" :version="$version" :depth="$depth + 1" />
    </div>
  @else
    @php
      $isActive = request()->is("docs/{$version}/{$item['url']}");
    @endphp

    <div class="mb-1">
      <a href="/docs/{{ $version }}/{{ $item['url'] }}" data-turbo="true" class="{{ $isActive ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 hover:border-l-blue-400 hover:underline" style="padding-left: {{ $depth * 12 }}px">{{ $item['label'] }}</a>
    </div>
  @endif
@endforeach
