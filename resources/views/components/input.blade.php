@props([
  'size' => 'base',
  'type' => 'text',
  'passManagerDisabled' => true,
  'required' => false,
  'id' => null,
  'label' => null,
  'autocomplete' => null,
  'error' => null,
  'placeholder' => null,
  'value' => null,
  'help' => null,
  'autofocus' => false,
  'disabled' => false,
])

@php
  $classes = [
    'block w-full appearance-none',
    'pr-3 pl-3',
    'bg-input',
    'text-ink placeholder-muted-soft disabled:text-muted',
    'rounded-md border border-hairline disabled:opacity-60',
    'shadow-xs disabled:shadow-none dark:shadow-none',
    'aria-invalid:border-error',
    match ($size) {
      'base' => 'h-10 py-2 text-base leading-[1.375rem] sm:text-sm',
      'sm' => 'h-8 py-1.5 text-sm leading-[1.125rem]',
      'xs' => 'h-6 py-1.5 text-xs leading-[1.125rem]',
    },
  ];
@endphp

@if ($label)
  <div class="space-y-2">
    <div class="flex items-center space-x-2">
      <x-label :for="$id" :value="$label" />
      @if (! $required)
        <span class="text-sm text-muted-soft">({{ __('Optional') }})</span>
      @endif
    </div>
    <input id="{{ $id }}" name="{{ $id }}" type="{{ $type }}" {{ $attributes->class($classes) }} value="{{ $value }}" {{ $autocomplete ? 'autocomplete="' . $autocomplete . '"' : '' }} placeholder="{{ $placeholder ? $placeholder : '' }}" @if($passManagerDisabled) data-1p-ignore @endif {{ $autofocus ? 'autofocus' : '' }} {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} />
    @if ($help)
      <p class="mt-1 block text-xs text-muted">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@else
  <div class="space-y-2">
    <input id="{{ $id }}" name="{{ $id }}" type="{{ $type }}" {{ $attributes->class($classes) }} value="{{ $value }}" {{ $autocomplete ? 'autocomplete="' . $autocomplete . '"' : '' }} placeholder="{{ $placeholder ? $placeholder : '' }}" @if($passManagerDisabled) data-1p-ignore @endif {{ $autofocus ? 'autofocus' : '' }} {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} />
    @if ($help)
      <p class="mt-1 block text-xs text-muted">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@endif
