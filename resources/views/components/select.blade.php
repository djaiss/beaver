@props([
  'required' => false,
  'id' => null,
  'label' => null,
  'error' => null,
  'value' => null,
  'help' => null,
  'helpId' => null,
  'helpAlign' => 'left',
  'options' => [],
  'selected' => null,
])

@php
  $classes = [
    'block w-full appearance-none',
    'pr-3 pl-3',
    'bg-input',
    'text-ink disabled:text-muted',
    'rounded-md border border-hairline disabled:opacity-60',
    'shadow-xs disabled:shadow-none dark:shadow-none',
    'aria-invalid:border-error',
    'h-10 py-2 text-base leading-[1.375rem] sm:text-sm',
  ];
@endphp

@if ($label)
  <div class="space-y-2">
    <div class="flex items-center space-x-2">
      <x-label :for="$id" :value="$label" />
      @if ($helpId)
        <x-help :id="$helpId" :align="$helpAlign" />
      @endif
      @if (! $required)
        <span class="text-sm text-muted-soft">({{ __('optional') }})</span>
      @endif
    </div>

    <select id="{{ $id }}" name="{{ $id }}" {{ $attributes->class($classes) }} {{ $required ? 'required' : '' }}>
      @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) $value === (string) $selected)>{{ $label }}</option>
      @endforeach
    </select>
    @if ($help)
      <p class="mt-1 block text-xs text-muted">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@else
  <div class="space-y-2">
    <select id="{{ $id }}" name="{{ $id }}" {{ $attributes->class($classes) }} {{ $required ? 'required' : '' }}>
      @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) $value === (string) $selected)>{{ $label }}</option>
      @endforeach
    </select>
    @if ($help)
      <p class="mt-1 block text-xs text-muted">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@endif
