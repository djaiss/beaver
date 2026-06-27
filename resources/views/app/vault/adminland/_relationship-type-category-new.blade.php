<x-form x-target="relationship-type-category-list add-relationship-type-category-form notifications" x-target.back="add-relationship-type-category-form" id="add-relationship-type-category-form" action="{{ route('vault.adminland.relationship_type_categories.create', ['vaultId' => $vault->id]) }}" method="post" class="space-y-5 border-b border-gray-200 p-4 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
  <x-input id="name" :label="__('app/vault.adminland.relationship_types.category_name')" type="text" required autofocus :error="$errors->get('name')" />

  <div class="flex justify-between">
    <x-button.secondary x-target="add-relationship-type-category-form" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">
      {{ __('app/shared.cancel') }}
    </x-button.secondary>

    <x-button class="mr-2">
      {{ __('app/shared.save') }}
    </x-button>
  </div>
</x-form>
