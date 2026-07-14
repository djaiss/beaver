@props([
  'href',
  'turbo' => false,
  'type' => 'submit',
])

@php
  $base = 'relative inline-flex h-10 transform-gpu cursor-pointer items-center justify-center gap-2 rounded-md text-sm font-semibold whitespace-nowrap transition duration-150 ease-out active:translate-y-[1px] active:scale-[0.97] active:shadow-inner active:ease-in disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-5';
  $visual = 'border border-hairline bg-canvas text-ink shadow-xs hover:bg-card';
  $classes = $base . ' ' . $visual;
@endphp

@isset($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($turbo) data-turbo="true" @endif>
    @isset($icon)
      <span class="shrink-0">{{ $icon }}</span>
    @endisset

    {{ $slot }}
  </a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon)
      <span class="shrink-0">{{ $icon }}</span>
    @endisset

    {{ $slot }}
  </button>
@endif
