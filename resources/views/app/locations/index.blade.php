<x-app-layout>
  <x-slot:title>
    {{ __('Locations') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Locations') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Where your items are physically stored.') }}</p>
      </div>

      <x-box padding="p-0">
        @forelse ($tree as $node)
          @include('app.locations._row', ['node' => $node, 'depth' => 0, 'parentOptions' => $parentOptions])
        @empty
          <x-empty-state>
            <x-slot:icon>
              @svg('lucide-map-pin', 'size-5 text-muted')
            </x-slot>
            {{ __('No locations yet. Add your first one below.') }}
          </x-empty-state>
        @endforelse
      </x-box>

      <x-box title="{{ __('Add a location') }}">
        <x-form method="post" :action="route('locations.create')" class="flex flex-col gap-3 sm:flex-row sm:items-end">
          <div class="flex-1">
            <x-input id="name" :label="__('Name')" placeholder="{{ __('e.g. Shelf A') }}" :error="$errors->get('name')" required data-test="new-location-name" />
          </div>
          <div class="w-full sm:w-48">
            <x-select id="parent_id" :label="__('Parent')" :options="$parentOptions" :selected="''" :error="$errors->get('parent_id')" />
          </div>
          <x-button type="submit" data-test="add-location-button">{{ __('Add location') }}</x-button>
        </x-form>
      </x-box>
    </div>
  </div>
</x-app-layout>
