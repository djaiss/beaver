<x-app-layout :vault="$vault">
  <x-slot:title>
    {{ __('Vault') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('vault.index')],
    ['label' => __('Vault')]
  ]" />

  {{ __('bla') }}
</x-app-layout>
