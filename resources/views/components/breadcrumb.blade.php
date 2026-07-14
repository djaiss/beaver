@props([
  'items',
])

<div class="flex w-full rounded-t-lg border-b border-hairline bg-canvas px-4 py-2">
  <div class="flex gap-x-2">
    <p class="text-muted">{{ __('You are here:') }}</p>
    @foreach ($items as $item)
      @if (isset($item['route']))
        <x-link href="{{ $item['route'] }}">{{ $item['label'] }}</x-link>
      @else
        <p class="text-muted">{{ $item['label'] }}</p>
      @endif
      @if (! $loop->last)
        <p class="text-muted">/</p>
      @endif
    @endforeach
  </div>
</div>
