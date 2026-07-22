<x-app-layout :collection="$collection">
  <x-slot:title>
    {{ __('Edit :name', ['name' => $item->name]) }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    @include('app.items.partials._form')
  </div>
</x-app-layout>
