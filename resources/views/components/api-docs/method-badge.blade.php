@props(['method'])

@php
  $classes = [
      'GET' => 'bg-blue-50 text-blue-600',
      'POST' => 'bg-emerald-50 text-emerald-600',
      'PUT' => 'bg-amber-50 text-amber-700',
      'DELETE' => 'bg-red-50 text-red-600',
  ][$method] ?? 'bg-gray-100 text-gray-600';
@endphp

<span {{ $attributes->merge(['class' => 'shrink-0 rounded-md px-1.5 py-1 font-mono text-[11px] font-bold '.$classes]) }}>{{ $method }}</span>
