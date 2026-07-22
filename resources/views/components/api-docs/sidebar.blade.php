@props(['navigation'])

@php
  $methodColors = [
      'GET' => 'text-blue-600',
      'POST' => 'text-emerald-600',
      'PUT' => 'text-amber-600',
      'DELETE' => 'text-red-600',
  ];
@endphp

<aside class="sticky top-[7.5rem] hidden h-[calc(100vh-7.5rem)] w-72 shrink-0 overflow-y-auto border-r border-gray-200 py-8 pr-5 pl-6 lg:block">
  <p class="mb-2 text-[11px] font-semibold tracking-wider text-gray-400 uppercase">Getting started</p>
  @foreach ($navigation['guides'] as $item)
    <a
      href="#{{ $item['id'] }}"
      x-show="query === '' || @js(mb_strtolower($item['label'])).includes(query.toLowerCase())"
      class="mb-px block rounded-md px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900"
    >{{ $item['label'] }}</a>
  @endforeach

  <p class="mt-7 mb-2 text-[11px] font-semibold tracking-wider text-gray-400 uppercase">Resources</p>
  @foreach ($navigation['resources'] as $group)
    <div class="mb-3.5">
      <a href="#{{ $group['items'][0]['id'] }}" class="block rounded-md px-2 py-1.5 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ $group['name'] }}</a>
      @foreach ($group['items'] as $item)
        <a
          href="#{{ $item['id'] }}"
          x-show="query === '' || @js(mb_strtolower($group['name'].' '.$item['label'])).includes(query.toLowerCase())"
          class="ml-2 flex items-center gap-2 border-l border-gray-200 py-1 pr-2 pl-5 text-[13px] text-gray-500 hover:text-gray-900"
        >
          <span class="w-10 shrink-0 font-mono text-[10px] font-bold {{ $methodColors[$item['method']] ?? 'text-gray-500' }}">{{ $item['method'] }}</span>
          <span>{{ $item['label'] }}</span>
        </a>
      @endforeach
    </div>
  @endforeach

  <div class="mt-6 border-t border-gray-100 pt-4">
    <a href="{{ route('marketing.index') }}" class="mb-2 block text-[13px] text-gray-400 hover:text-gray-700">← Back to {{ config('app.name') }}</a>
    <a href="{{ route('profile.api-keys.new') }}" class="block text-[13px] text-gray-400 hover:text-gray-700">Manage API tokens</a>
  </div>
</aside>
