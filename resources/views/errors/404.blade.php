@php
  $home = auth()->check() ? route('dashboard.index') : route('login');
@endphp

<x-errors.layout
  code="404"
  :name="__('Not found')"
  accent="#fb923c"
  accent-soft="#fdba74"
  :badge="__('no such page')"
  :headline="__('This page went missing from the collection')"
  :body="__('We couldn’t find what you were looking for. It may have been moved, renamed, or deleted, or the link that brought you here is out of date.')"
  :primary-label="__('Back to dashboard')"
  :primary-href="$home"
  :secondary-label="__('Browse collections')"
  :secondary-href="route('collections.index')"
>
  <x-slot:context>
    <x-errors.row :label="__('Requested path')" :value="'/'.request()->path()" mono />
  </x-slot:context>
</x-errors.layout>
