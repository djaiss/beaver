<x-box padding="p-0">
  <x-slot:title>{{ __('app/vault.adminland.edit_title') }}</x-slot>

  <x-form method="put" action="{{ route('vault.adminland.update', ['vaultId' => $vault->id]) }}" x-target="vault-details notifications header-vault-name" x-target.back="vault-details">
    <!-- name -->
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app/vault.adminland.vault_name') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="vault_name" type="text" name="vault_name" value="{{ old('vault_name', $vault->name) }}" required :error="$errors->get('vault_name')" autofocus />
      </div>
    </div>

    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('app/shared.save') }}</x-button>
    </div>
  </x-form>
</x-box>
