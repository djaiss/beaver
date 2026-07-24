<x-app-layout :catalog="$catalog">
  <x-slot:title>
    {{ __('Add an item') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    @include('app.items.partials._form', ['item' => null])
  </div>
</x-app-layout>
