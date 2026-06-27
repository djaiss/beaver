<x-box padding="p-0">
  <x-slot:title>{{ __('Destroy vault') }}</x-slot>

  <x-slot:description>
    <p>{{ __('This action is irreversible. All vault data will be permanently deleted immediately.') }}</p>
    <p>{{ __('The data itself, while encrypted, will remain in our backups for 30 days before being permanently deleted.') }}</p>
  </x-slot>

  <div id="new-api-key-form" class="flex items-center justify-between rounded-t-lg p-3 last:rounded-b-lg last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-800">
    <p class="text-sm text-zinc-500">{{ __('Please be certain. This action cannot be undone.') }}</p>

    <x-form action="{{ route('vault.adminland.manage.destroy', ['vaultId' => $vault->id]) }}" method="delete" onsubmit="return confirm('{{ __('Are you sure you want to proceed? This can not be undone.') }}');">
      <x-button.secondary class="text-sm">
        {{ __('Delete') }}
      </x-button.secondary>
    </x-form>
  </div>
</x-box>
