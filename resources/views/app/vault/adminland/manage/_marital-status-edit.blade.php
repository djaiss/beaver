<x-form x-target="marital-status-{{ $maritalStatus->id }} notifications" x-target.back="marital-status-{{ $maritalStatus->id }}" id="marital-status-{{ $maritalStatus->id }}" action="{{ route('vault.adminland.marital-statuses.update', ['vaultId' => $vault->id, 'maritalStatus' => $maritalStatus->id]) }}" method="put" class="space-y-5 p-4 hover:bg-blue-50 dark:hover:bg-gray-800">
  <div>
    <x-input id="name" :label="__('app/vault.adminland.marital-statuses.name')" type="text" :value="old('name', $maritalStatus->name)" required autofocus :error="$errors->get('name')" />
  </div>

  <div class="flex justify-between">
    <x-button.secondary x-target="marital-status-{{ $maritalStatus->id }}" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">
      {{ __('app/shared.cancel') }}
    </x-button.secondary>

    <x-button class="mr-2">
      {{ __('app/shared.save') }}
    </x-button>
  </div>
</x-form>
