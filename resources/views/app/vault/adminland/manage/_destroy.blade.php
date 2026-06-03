<x-box padding="p-0">
  <x-slot:title>{{ __('app/vault.adminland.destroy_vault') }}</x-slot>

  <x-slot:description>
    <p>{{ __('app/vault.adminland.destroy_vault_description') }}</p>
    <p>{{ __('app/vault.adminland.destroy_vault_30_days') }}</p>
  </x-slot>

  <div id="new-api-key-form" class="flex items-center justify-between rounded-t-lg p-3 last:rounded-b-lg last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-800">
    <p class="text-sm text-zinc-500">{{ __('app/vault.adminland.destroy_vault_confirm') }}</p>

    <x-form action="{{ route('vault.adminland.manage.destroy', ['vaultId' => $vault->id]) }}" method="delete" onsubmit="return confirm('{{ __('app/settings/security.api.confirm_delete') }}');">
      <x-button.secondary class="text-sm">
        {{ __('app/shared.delete') }}
      </x-button.secondary>
    </x-form>
  </div>
</x-box>
