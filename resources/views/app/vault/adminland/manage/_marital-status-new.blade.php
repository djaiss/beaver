<x-form x-target="marital-status-list add-marital-status-form notifications" x-target.back="add-marital-status-form" id="add-marital-status-form" action="{{ route('vault.adminland.marital-statuses.create', ['vaultId' => $vault->id]) }}" method="post" class="space-y-5 border-b border-gray-200 p-4 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
  <div>
    <x-input id="name" :label="__('app/vault.adminland.marital-statuses.name')" type="text" required autofocus :error="$errors->get('name')" />
  </div>

  <div class="flex justify-between">
    <x-button.secondary x-target="add-marital-status-form" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">
      {{ __('app/shared.cancel') }}
    </x-button.secondary>

    <x-button class="mr-2">
      {{ __('app/shared.save') }}
    </x-button>
  </div>
</x-form>
