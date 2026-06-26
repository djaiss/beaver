<x-app-layout>
  <x-slot:title>
    {{ __('app/vault.join.title') }}
  </x-slot:title>

  <x-breadcrumb
    :items="[
    ['label' => __('app/breadcrumb.dashboard'), 'route' => route('vault.index')],
    ['label' => __('app/breadcrumb.join_vault')]
  ]" />

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-xl items-start justify-center">
      <x-box title="{{ __('app/vault.join.title') }}">
        <x-form method="post" :action="route('vault.join.store')" class="space-y-4">
          <x-input id="invitation_code" name="invitation_code" :label="__('app/vault.join.invitation_code')" :help="__('app/vault.join.invitation_code_help')" :error="$errors->get('invitation_code')" required autofocus />

          <div class="flex items-center justify-between">
            <x-button.secondary href="{{ route('vault.index') }}" turbo="true">{{ __('app/shared.cancel') }}</x-button.secondary>

            <x-button type="submit">{{ __('app/vault.join.submit') }}</x-button>
          </div>
        </x-form>
      </x-box>
    </div>
  </div>
</x-app-layout>
