@props([
  'rows' => 3,
  'required' => false,
  'id' => null,
  'label' => null,
  'error' => null,
  'placeholder' => null,
  'value' => null,
  'help' => null,
  'autofocus' => false,
])

@php
  $classes = [
    'block w-full appearance-none resize-y',
    'px-3 py-2.5',
    'bg-input',
    'text-ink placeholder-muted-soft',
    'rounded-md border border-hairline',
    'shadow-xs dark:shadow-none',
    'aria-invalid:border-error',
    'text-base leading-[1.375rem] sm:text-sm',
  ];
@endphp

<div class="space-y-2">
  @if ($label)
    <div class="flex items-center space-x-2">
      <x-label :for="$id" :value="$label" />
      @if (! $required)
        <span class="text-sm text-muted-soft">({{ __('Optional') }})</span>
      @endif
    </div>
  @endif

  <textarea id="{{ $id }}" name="{{ $id }}" rows="{{ $rows }}" {{ $attributes->class($classes) }} placeholder="{{ $placeholder ? $placeholder : '' }}" {{ $autofocus ? 'autofocus' : '' }} {{ $required ? 'required' : '' }}>{{ $value }}</textarea>

  @if ($help)
    <p class="mt-1 block text-xs text-muted">{{ $help }}</p>
  @endif

  <x-error :messages="$error" />
</div>
