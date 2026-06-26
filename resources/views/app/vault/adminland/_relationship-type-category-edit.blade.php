<x-form x-target="relationship-type-category-{{ $relationshipTypeCategory->id }} notifications" x-target.back="relationship-type-category-{{ $relationshipTypeCategory->id }}" id="relationship-type-category-{{ $relationshipTypeCategory->id }}" action="{{ route('vault.adminland.relationship_type_categories.update', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id]) }}" method="put" class="space-y-5 p-4 hover:bg-blue-50 dark:hover:bg-gray-800">
  <x-input id="name" :label="__('app/vault.adminland.relationship_types.category_name')" type="text" :value="old('name', $relationshipTypeCategory->name)" required autofocus :error="$errors->get('name')" />

  <div class="flex justify-between">
    <x-button.secondary x-target="relationship-type-category-{{ $relationshipTypeCategory->id }}" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">{{ __('app/shared.cancel') }}</x-button.secondary>

    <x-button class="mr-2">{{ __('app/shared.save') }}</x-button>
  </div>
</x-form>
