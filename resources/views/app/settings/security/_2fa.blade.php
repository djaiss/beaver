<?php
/**
 * @var \App\ViewModels\Settings\SecurityIndexViewModel $view
 */
?>

<x-box padding="p-0">
  <!-- Authenticator app -->
  <div id="authenticator-app" class="flex items-center rounded-t-lg p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
    <x-phosphor-device-mobile class="h-5 w-5 text-gray-500" />
    <div class="ms-5 flex w-full items-center justify-between">
      <div>
        <p class="font-semibold">
          {{ __('app/settings/security.two_factor.authenticator_app') }}
          @if ($view->has2fa())
            <span class="ml-2 rounded-sm bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">{{ __('app/settings/security.two_factor.configured') }}</span>
          @endif
        </p>
        <p class="text-xs text-gray-600">{{ __('app/settings/security.two_factor.description') }}</p>
      </div>

      @if ($view->has2fa())
        <x-form onsubmit="return confirm('{{ __('app/settings/security.two_factor.confirm_remove') }}');" action="{{ $view->url()->destroy2fa }}" method="delete">
          <x-button.secondary x-target="authenticator-app" class="mr-2 text-sm">{{ __('app/shared.remove') }}</x-button.secondary>
        </x-form>
      @else
        <x-button.secondary href="{{ $view->url()->new2fa }}" x-target="authenticator-app" class="mr-2 text-sm">{{ __('app/settings/security.two_factor.set_up') }}</x-button.secondary>
      @endif
    </div>
  </div>

  <!-- recovery codes -->
  @if ($view->has2fa())
    <div id="recovery-codes" class="flex items-center rounded-b-lg border-t border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <x-phosphor-toolbox class="h-5 w-5 text-gray-500" />
      <div class="ms-5 flex w-full items-center justify-between">
        <div>
          <p class="font-semibold">{{ __('app/settings/security.two_factor.recovery_codes') }}</p>
          <p class="text-xs text-gray-600">{{ __('app/settings/security.two_factor.recovery_codes_description') }}</p>
        </div>

        <x-button.secondary turbo="true" href="{{ $view->url()->showRecoveryCodes }}" x-target="recovery-codes" class="mr-2 text-sm">{{ __('app/shared.show') }}</x-button.secondary>
      </div>
    </div>
  @endif
</x-box>
