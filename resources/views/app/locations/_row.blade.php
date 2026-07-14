@php
  $location = $node['location'];
  $rowOptions = collect($parentOptions)
    ->except([$location->id])
    ->all();
@endphp

<div class="border-b border-hairline-soft last:border-b-0">
  <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between" style="padding-left: {{ 16 + $depth * 24 }}px" data-test="location-row-{{ $location->id }}">
    <div class="flex min-w-0 items-center gap-2">
      @svg('lucide-map-pin', 'size-4 shrink-0 text-muted')
      <span class="truncate text-sm font-semibold text-ink">{{ $location->name }}</span>
    </div>

    <div class="flex items-center gap-2">
      <x-form method="put" :action="route('locations.update', $location->id)" class="flex items-center gap-2">
        <input name="name" value="{{ $location->name }}" class="h-9 w-40 min-w-0 rounded-md border border-hairline bg-input px-3 text-sm text-ink" data-test="location-name-{{ $location->id }}" />
        <x-select id="parent_id" :options="$rowOptions" :selected="(string) $location->parent_id" class="h-9" />
        <x-button type="submit" class="text-sm" data-test="save-location-{{ $location->id }}">{{ __('Save') }}</x-button>
      </x-form>

      <x-form method="delete" :action="route('locations.destroy', $location->id)" onsubmit="return confirm('{{ __('Delete this location? Nested locations will be deleted too. This cannot be undone.') }}')">
        <x-button.secondary type="submit" class="text-sm" data-test="delete-location-{{ $location->id }}">{{ __('Delete') }}</x-button.secondary>
      </x-form>
    </div>
  </div>

  @foreach ($node['children'] as $child)
    @include('app.locations._row', ['node' => $child, 'depth' => $depth + 1, 'parentOptions' => $parentOptions])
  @endforeach
</div>
