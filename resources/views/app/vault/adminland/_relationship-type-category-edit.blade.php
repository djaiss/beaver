<x-form x-target="relationship-type-category-{{ $relationshipTypeCategory->id }} notifications" x-target.back="relationship-type-category-{{ $relationshipTypeCategory->id }}" id="relationship-type-category-{{ $relationshipTypeCategory->id }}" action="{{ route('vault.adminland.relationship_type_categories.update', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id]) }}" method="put" class="space-y-5 p-4 hover:bg-blue-50 dark:hover:bg-gray-800">
  <x-input id="name" :label="__('Name of the relationship type category')" type="text" :value="old('name', $relationshipTypeCategory->name)" required autofocus :error="$errors->get('name')" />

  <div class="flex justify-between">
    <x-button.secondary x-target="relationship-type-category-{{ $relationshipTypeCategory->id }}" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">
      {{ __('Cancel') }}
    </x-button.secondary>

    <x-button class="mr-2">
      {{ __('Save') }}
    </x-button>
  </div>
</x-form>
