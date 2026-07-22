@php
  $home = auth()->check() ? route('dashboard.index') : route('login');
@endphp

<x-errors.layout
  code="403"
  :name="__('Access denied')"
  accent="#ec4899"
  accent-soft="#f472b6"
  :badge="__('permission required')"
  :headline="__('You don’t have access to this page')"
  :body="__('This area is restricted to members with the right role. Your current permissions don’t include it. Ask an account owner to grant you access.')"
  :primary-label="__('Back to dashboard')"
  :primary-href="$home"
>
  @auth
    <x-slot:context>
      <x-errors.row :label="__('Your role')" :value="ucfirst(auth()->user()->role)" />
    </x-slot:context>
  @endauth
</x-errors.layout>
