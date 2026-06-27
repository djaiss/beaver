@props([
    'name',
    'size' => 40,
    'class' => '',
])

@php
    $initials = collect(explode(' ', trim($name)))
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');

    $avatar = new \App\Actions\CreateAvatarSVG($initials, $size);
@endphp

<span
    {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center ' . $class]) }}
    style="width: {{ $size }}px; height: {{ $size }}px;"
    title="{{ $name }}"
>
    {!! $avatar->render() !!}
</span>
