<x-app-layout :vault="$vault">
  <x-slot:title>
    {{ __('app/vault.show.title') }}
  </x-slot:title>

  <x-breadcrumb
    :items="[
    ['label' => __('app/breadcrumb.dashboard'), 'route' => route('vault.index')],
    ['label' => __('app/breadcrumb.vault')]
  ]" />

  {{ __('app/vault.show.placeholder') }}
</x-app-layout>
