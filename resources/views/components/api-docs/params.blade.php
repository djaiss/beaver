@props(['title', 'params'])

@if ($params !== [])
  <div class="mt-7">
    <p class="mb-3.5 text-[17px] font-bold text-gray-900">{{ $title }}</p>
    <div class="border-t border-gray-200"></div>
    @foreach ($params as $param)
      <div class="border-b border-gray-100 py-4">
        <div class="mb-2 flex flex-wrap items-baseline gap-2.5">
          <span class="font-mono text-base font-bold text-gray-900">{{ $param['name'] }}</span>
          <span class="font-mono text-[13px] text-gray-400">{{ $param['type'] }}</span>
          <span class="font-mono text-[13px] {{ $param['required'] ? 'text-pink-500' : 'text-gray-400' }}">{{ $param['required'] ? 'required' : 'optional' }}</span>
        </div>
        <p class="text-[15px] leading-relaxed text-gray-700">{{ $param['description'] }}</p>
        @if (isset($param['default']))
          <p class="mt-2 text-sm text-gray-400">Default: <span class="rounded border border-gray-200 bg-gray-100 px-1.5 py-0.5 font-mono text-[13px] text-gray-700">{{ $param['default'] }}</span></p>
        @endif
      </div>
    @endforeach
  </div>
@endif
