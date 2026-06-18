<x-form x-target="gender-{{ $gender->id }} notifications" x-target.back="gender-{{ $gender->id }}" id="gender-{{ $gender->id }}" action="{{ route('vault.adminland.genders.update', ['vaultId' => $vault->id, 'gender' => $gender->id]) }}" method="put" class="space-y-5 p-4 hover:bg-blue-50 dark:hover:bg-gray-800">
  <div>
    <x-input id="name" :label="__('app/vault.adminland.genders.name')" type="text" :value="old('name', $gender->name)" required autofocus :error="$errors->get('name')" />
  </div>

  <div class="flex justify-between">
    <x-button.secondary x-target="gender-{{ $gender->id }}" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">
      {{ __('app/shared.cancel') }}
    </x-button.secondary>

    <x-button class="mr-2">
      {{ __('app/shared.save') }}
    </x-button>
  </div>
</x-form>
