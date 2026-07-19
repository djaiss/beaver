{{--
    The avatar of a user: their uploaded image when they have one, and the
    generated initials otherwise. Sizes must be one of User::AVATAR_SIZES, as
    those are the ones written to disk on upload.
--}}
@props([
    'user' => null,
    'name' => null,
    'size' => 32,
])

@php
    $displayName = $name ?? $user?->getFullName() ?? '?';
@endphp

@if($user?->hasAvatar())
    <img
        src="{{ $user->avatarUrl($size) }}"
        srcset="{{ $user->avatarSrcset($size) }}"
        width="{{ $size }}"
        height="{{ $size }}"
        alt="{{ $displayName }}"
        title="{{ $displayName }}"
        {{ $attributes->class('shrink-0 rounded-full object-cover') }}
    >
@else
    <x-avatar-initials :name="$displayName" {{ $attributes }} />
@endif
