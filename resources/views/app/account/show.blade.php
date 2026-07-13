<x-app-layout>
  <x-slot:title>
    {{ $account->name }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Accounts'), 'route' => route('accounts.index')],
    ['label' => $account->name]
  ]" />

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-xl items-start justify-center space-y-6">
      <x-box title="{{ $account->name }}">
        <p class="text-sm text-gray-700 dark:text-gray-300">
          {{ __('You are a :role of this account.', ['role' => __(ucfirst($member->role))]) }}
        </p>

        <div class="mt-4">
          <x-button.secondary href="{{ route('accounts.members.index', $account->id) }}" turbo="true">
            {{ __('Manage members') }}
          </x-button.secondary>
        </div>
      </x-box>

      @if ($member->role === 'owner')
        <x-box title="{{ __('Rename account') }}">
          <x-form method="put" :action="route('accounts.update', $account->id)" class="space-y-4">
            <x-input id="name" name="name" :label="__('Name')" :value="$account->name" :error="$errors->get('name')" required />

            <div class="flex items-center justify-end">
              <x-button type="submit">
                {{ __('Save') }}
              </x-button>
            </div>
          </x-form>
        </x-box>

        <x-box title="{{ __('Danger zone') }}">
          <p class="mb-4 text-sm text-gray-700 dark:text-gray-300">
            {{ __('Deleting the account permanently removes it along with everything it contains.') }}
          </p>

          <x-form method="delete" :action="route('accounts.destroy', $account->id)">
            <x-button.secondary type="submit">
              {{ __('Delete account') }}
            </x-button.secondary>
          </x-form>
        </x-box>
      @endif
    </div>
  </div>
</x-app-layout>
