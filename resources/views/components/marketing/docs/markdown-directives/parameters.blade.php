<div x-cloak x-data="{ open: false }" class="mb-2">
  <div @click="open = !open" x-bind:class="open ? 'border-b border-gray-200 dark:border-gray-700' : ''" class="mb-2 flex cursor-pointer items-center justify-between pb-2">
    <p class="font-semibold">{{ $options['title'] ?? 'Parameters' }}</p>
    <x-lucide-chevron-right x-bind:class="open ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
  </div>

  <div x-show="open" x-transition class="mt-2">
    @if (isset($options['empty']))
      <p class="text-gray-500">{{ $options['empty'] }}</p>
    @else
      {!! $content !!}
    @endif
  </div>
</div>
