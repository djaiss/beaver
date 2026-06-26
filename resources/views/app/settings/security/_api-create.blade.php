<?php
/**
 * @var \App\ViewModels\Settings\ApiKeyCreateViewModel $view
 */
?>

<x-form action="{{ $view->url()->store }}" id="new-api-key-form" x-target="api-key-list new-api-key-form api-key-notification notifications" x-target.back="new-api-key-form" method="post" class="space-y-5 rounded-t-lg p-4 first:rounded-t-lg last:rounded-b-lg last:border-0 hover:bg-blue-50 dark:hover:bg-gray-800">
  <div>
    <x-input id="label" :label="__('app/settings/security.api.label')" type="text" name="label" required autofocus :error="$errors->get('label')" />
  </div>

  <div class="flex justify-between">
    <x-button.secondary href="{{ $view->url()->settings }}" turbo="true" x-target="new-api-key-form">{{ __('app/shared.cancel') }}</x-button.secondary>

    <x-button class="mr-2" data-test="create-api-key-button">{{ __('app/shared.create') }}</x-button>
  </div>
</x-form>
