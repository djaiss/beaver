<x-app-layout>
  <x-slot:title>
    {{ __('Create vault') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('vault.index')],
    ['label' => __('Create vault')]
  ]" />

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-xl items-start justify-center">
      <x-box title="{{ __('Create vault') }}">
        <x-form method="post" :action="route('vault.store')" class="space-y-4">
          <x-input id="vault_name" name="vault_name" :label="__('Name')" :help="__('The name can only contain letters, numbers, spaces, hyphens, and underscores.')" :error="$errors->get('vault_name')" required placeholder="Dunder Mifflin" autofocus />

          <div class="flex items-center justify-between">
            <x-button.secondary href="{{ route('vault.index') }}" turbo="true">
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
