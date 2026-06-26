<?php
/**
 * @var \App\ViewModels\Settings\AccountIndexViewModel $view
 */
?>

<div class="mb-2 flex items-center gap-x-2">
  <x-phosphor-warning class="h-4 w-4 text-red-600 dark:text-red-400" />
  <h2 class="font-semi-bold mb-1 text-lg text-red-600 dark:text-red-400">{{ __('app/settings/account.delete.title') }}</h2>
</div>

<!-- Danger Zone -->
<div class="rounded-md border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950">
  <div class="p-4">
    <p class="mb-4 text-sm text-red-600 dark:text-red-400">{{ __('app/settings/account.delete.warning') }}</p>

    @if ($view->vaultsToDelete()->isNotEmpty())
      <p class="mb-4 text-sm text-red-600 dark:text-red-400">{{ __('app/settings/account.delete.only_owner_vaults') }}</p>
      <div class="rounded-lg border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950 mb-4">
        @foreach ($view->vaultsToDelete() as $vault)
          <div class="rounded-0 flex items-center justify-between border-b border-red-50 first:rounded-t-lg last:rounded-b-lg last:border-b-0 bg-white dark:border-gray-700 dark:hover:bg-gray-800">
            <div class="flex items-center">
              <div class="mr-2 rounded-full p-3">
                <img src="{{ $vault->avatar }}" class="h-8 w-8" alt="{{ __('app/settings/account.vault_avatar_alt') }}" />
              </div>

              <x-link href="{{ $vault->link }}">{{ $vault->name }}</x-link>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    @if ($view->vaultsNotDeleted()->isNotEmpty())
      <p class="mb-4 text-sm text-red-600 dark:text-red-400">{{ __('app/settings/account.delete.other_owner_vaults') }}</p>
      <div class="rounded-lg border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950 mb-4">
        @foreach ($view->vaultsNotDeleted() as $vault)
          <div class="rounded-0 flex items-center justify-between border-b border-gray-200 first:rounded-t-lg last:rounded-b-lg last:border-b-0 bg-white dark:border-gray-700 dark:hover:bg-gray-800">
            <div class="flex items-center">
              <div class="mr-2 rounded-full p-3">
                <img src="{{ $vault->avatar }}" class="h-8 w-8" alt="{{ __('app/settings/account.vault_avatar_alt') }}" />
              </div>

              <x-link href="{{ $vault->link }}">{{ $vault->name }}</x-link>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <form
      action="{{ $view->url()->deleteAccount }}"
      method="post"
      x-data="{
      feedback: '',
      isValid: false,
      async handleSubmit() {
        if (! this.isValid) return

        if (await confirm('{{ __('app/settings/account.delete.confirm') }}')) {
          $el.submit()
        }
      },
    }"
      @submit.prevent="handleSubmit">
      @csrf
      @method ('delete')

      <label for="feedback" class="mt-4 block text-sm font-medium text-red-700 dark:text-red-400">{{ __('app/settings/account.delete.feedback_label') }}</label>

      <div class="mt-1">
        <textarea id="feedback" name="feedback" rows="3" x-model="feedback" @input="isValid = feedback.length >= 3" class="block w-full rounded-md border p-2 border-red-300 bg-white text-red-900 shadow-xs focus:border-red-500 focus:ring-red-500 dark:border-red-700 dark:bg-gray-800 dark:text-red-100 dark:focus:border-red-500 dark:focus:ring-red-500 sm:text-sm" placeholder="{{ __('app/settings/account.delete.feedback_placeholder') }}"></textarea>
      </div>

      <div class="mt-4 flex items-center justify-end gap-x-3">
        <button type="submit" x-bind:disabled="!isValid" x-bind:class="!isValid ? 'opacity-50 cursor-not-allowed' : ''" class="inline-flex items-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
          <x-phosphor-trash class="h-4 w-4" />
          {{ __('app/settings/account.delete.button') }}
        </button>
      </div>
    </form>
  </div>
</div>
