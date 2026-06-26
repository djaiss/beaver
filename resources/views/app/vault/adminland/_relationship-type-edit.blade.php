<x-form x-data="{ isDirected: {{ old('is_directed', $relationshipType->is_directed) ? 'true' : 'false' }} }" x-target="relationship-type-{{ $relationshipType->id }} notifications" x-target.back="relationship-type-{{ $relationshipType->id }}" id="relationship-type-{{ $relationshipType->id }}" action="{{ route('vault.adminland.relationship_types.update', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id, 'relationshipType' => $relationshipType->id]) }}" method="put" class="space-y-5 p-4 hover:bg-blue-50 dark:hover:bg-gray-800">
  <x-input id="name" :label="__('app/vault.adminland.relationship_types.type_name')" type="text" :value="old('name', $relationshipType->name)" required autofocus :error="$errors->get('name')" />

  <label class="flex items-start gap-3 text-sm text-gray-700 dark:text-gray-300">
    <input x-model="isDirected" type="checkbox" name="is_directed" value="1" @checked (old('is_directed', $relationshipType->is_directed)) class="mt-0.5 rounded-sm border-gray-300 text-indigo-600 shadow-xs focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900" />
    <span>
      <span class="block font-medium text-gray-900 dark:text-gray-100">{{ __('app/vault.adminland.relationship_types.is_directed') }}</span>
      <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.relationship_types.is_directed_help') }}</span>
    </span>
  </label>

  <div x-cloak x-show="isDirected" class="grid gap-5 sm:grid-cols-2">
    <x-input x-bind:disabled="!isDirected" id="forward_name" :label="__('app/vault.adminland.relationship_types.forward_name')" type="text" :value="old('forward_name', $relationshipType->forward_name)" required :error="$errors->get('forward_name')" />
    <x-input x-bind:disabled="!isDirected" id="reverse_name" :label="__('app/vault.adminland.relationship_types.reverse_name')" type="text" :value="old('reverse_name', $relationshipType->reverse_name)" required :error="$errors->get('reverse_name')" />
  </div>

  <div class="flex justify-between">
    <x-button.secondary x-target="relationship-type-{{ $relationshipType->id }}" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">{{ __('app/shared.cancel') }}</x-button.secondary>

    <x-button class="mr-2">{{ __('app/shared.save') }}</x-button>
  </div>
</x-form>
