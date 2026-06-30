<x-form id="new-webhook-form" x-target="webhook-list new-webhook-form notifications" x-target.back="new-webhook-form" action="{{ route('settings.webhooks.create') }}" method="post" class="space-y-5 rounded-t-lg p-4 first:rounded-t-lg last:rounded-b-lg last:border-0 hover:bg-blue-50 dark:hover:bg-gray-800">
  <div class="space-y-4">
    <x-input id="url" :label="__('Endpoint URL')" type="url" name="url" placeholder="https://example.com/webhooks" required autofocus :error="$errors->get('url')" />

    <x-input id="label" :label="__('Label')" type="text" name="label" :error="$errors->get('label')" />
  </div>

  <div class="flex justify-between">
    <x-button.secondary href="{{ route('settings.webhooks.index') }}" turbo="true" x-target="new-webhook-form">
      {{ __('Cancel') }}
    </x-button.secondary>

    <x-button class="mr-2" data-test="create-webhook-button">
      {{ __('Create') }}
    </x-button>
  </div>
</x-form>
