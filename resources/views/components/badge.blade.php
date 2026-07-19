@props(['color' => null])

@php
    $classes = match ($color) {
        'orange' => 'bg-badge-orange text-white',
        'pink' => 'bg-badge-pink text-white',
        'violet' => 'bg-badge-violet text-white',
        'emerald' => 'bg-badge-emerald text-white',
        'success' => 'bg-success/10 text-success',
        'error' => 'bg-error/10 text-error',
        default => 'bg-card text-ink',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-3 py-1 text-[13px] font-medium whitespace-nowrap', $classes]) }}>{{ $slot }}</span>
