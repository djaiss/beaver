<x-app-layout>
  <x-slot:title>
    {{ __('app/vault.create.title') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('app/breadcrumb.dashboard'), 'route' => route('vault.index')],
    ['label' => __('app/breadcrumb.create_vault')]
  ]" />

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-xl items-start justify-center">
      <x-box title="{{ __('app/vault.create.title') }}">
        <x-form method="post" :action="route('vault.create')" class="space-y-4">
          <x-input id="vault_name" name="vault_name" :label="__('app/vault.create.name')" :help="__('app/vault.create.name_help')" :error="$errors->get('vault_name')" required placeholder="Dunder Mifflin" autofocus />

          <div class="flex items-center justify-between">
            <x-button.secondary href="{{ route('vault.index') }}" turbo="true">
              {{ __('app/shared.cancel') }}
            </x-button.secondary>

            <x-button type="submit">
              {{ __('app/shared.create') }}
            </x-button>
          </div>
        </x-form>
      </x-box>
    </div>
  </div>
</x-app-layout>
