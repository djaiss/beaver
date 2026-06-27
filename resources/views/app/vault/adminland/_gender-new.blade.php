<x-form x-target="gender-list add-gender-form notifications" x-target.back="add-gender-form" id="add-gender-form" action="{{ route('vault.adminland.genders.create', ['vaultId' => $vault->id]) }}" method="post" class="space-y-5 border-b border-gray-200 p-4 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
  <div>
    <x-input id="name" :label="__('Name of the gender')" type="text" required autofocus :error="$errors->get('name')" />
  </div>

  <div class="flex justify-between">
    <x-button.secondary x-target="add-gender-form" href="{{ route('vault.adminland.index', ['vaultId' => $vault->id]) }}">
      {{ __('Cancel') }}
    </x-button.secondary>

    <x-button class="mr-2">
      {{ __('Save') }}
    </x-button>
  </div>
</x-form>
