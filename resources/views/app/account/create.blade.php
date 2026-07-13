<x-app-layout>
  <x-slot:title>
    {{ __('Create account') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Accounts'), 'route' => route('accounts.index')],
    ['label' => __('Create account')]
  ]" />

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-xl items-start justify-center">
      <x-box title="{{ __('Create account') }}">
        <x-form method="post" :action="route('accounts.create')" class="space-y-4">
          <x-input id="name" name="name" :label="__('Name')" :error="$errors->get('name')" required placeholder="Dunder Mifflin" autofocus />

          <div class="flex items-center justify-between">
            <x-button.secondary href="{{ route('accounts.index') }}" turbo="true">
              {{ __('Cancel') }}
            </x-button.secondary>

            <x-button type="submit">
              {{ __('Create') }}
            </x-button>
          </div>
        </x-form>
      </x-box>
    </div>
  </div>
</x-app-layout>
