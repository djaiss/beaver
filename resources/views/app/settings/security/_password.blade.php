<?php
/**
 * @var \App\ViewModels\Settings\SecurityIndexViewModel $view
 */
?>

<x-box padding="p-0">
  <x-slot:title>
    {{ __('app/settings/security.password.title') }}
  </x-slot:title>

  <x-form method="put" action="{{ $view->url()->updatePassword }}">
    <!-- current password -->
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app/settings/security.password.current') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="current_password" type="password" required :error="$errors->get('current_password')" autofocus />
      </div>
    </div>

    <!-- new password -->
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app/settings/security.password.new') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="new_password" type="password" help="{{ __('app/settings/security.password.minimum') }}" passwordrules="minlength: 8" required :error="$errors->get('new_password')" :passManagerDisabled="false" />
      </div>
    </div>

    <!-- confirm new password -->
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app/settings/security.password.confirm') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="new_password_confirmation" type="password" name="new_password_confirmation" required :error="$errors->get('new_password_confirmation')" />
      </div>
    </div>

    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('app/shared.save') }}</x-button>
    </div>
  </x-form>
</x-box>
