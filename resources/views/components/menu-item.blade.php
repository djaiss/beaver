{{-- One entry inside a dropdown menu, such as the one x-button.split opens. --}}
@props([
  'href' => null,
  'type' => 'submit',
  'turbo' => false,
  'danger' => false,
])

@php
  $base = 'flex w-full cursor-pointer items-center gap-2.5 rounded-md px-3 py-2 text-left text-sm font-medium transition-colors';
  $visual = $danger ? 'text-error hover:bg-error/10' : 'text-body hover:bg-card hover:text-ink';
  $classes = $base . ' ' . $visual;
@endphp

@isset($href)
  <a href="{{ $href }}" role="menuitem" {{ $attributes->merge(['class' => $classes]) }} @if($turbo) data-turbo="true" @endif>
    @isset($icon)
      <span class="shrink-0">{{ $icon }}</span>
    @endisset

    {{ $slot }}
  </a>
@else
  <button type="{{ $type }}" role="menuitem" {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon)
      <span class="shrink-0">{{ $icon }}</span>
    @endisset

    {{ $slot }}
  </button>
@endisset
