@props(['name' => '?'])

@php
    $parts = preg_split('/\s+/', trim((string) $name)) ?: [];
    $initials = collect($parts)
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

    if ($initials === '') {
        $initials = '?';
    }

    $palette = ['#8b5cf6', '#fb923c', '#34d399', '#3b82f6', '#ec4899'];
    $color = $palette[abs(crc32((string) $name)) % count($palette)];
@endphp

<span
    {{ $attributes->class('inline-flex shrink-0 items-center justify-center rounded-full font-medium text-white') }}
    style="background-color: {{ $color }};"
>{{ $initials }}</span>
