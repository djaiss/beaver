@props([
  'value',
])

<label {{ $attributes->class(['block text-sm leading-tight font-medium text-ink']) }}>{{ $value ?? $slot }}</label>
