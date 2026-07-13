<div class="mb-2 flex items-center gap-x-2">
  <x-phosphor-warning class="h-4 w-4 text-red-600 dark:text-red-400" />
  <h2 class="font-semi-bold mb-1 text-lg text-red-600 dark:text-red-400">
    {{ __('Delete your account') }}
  </h2>
</div>

<!-- Danger Zone -->
<div class="rounded-md border border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950">
  <div class="p-4">
    <p class="mb-4 text-sm text-red-600 dark:text-red-400">
      {{ __('Your account and all data will be deleted immediately and cannot be restored. This is irreversible. Please be certain.') }}
    </p>

    <form action="{{ route('settings.user.destroy') }}" method="post" x-data="{
      feedback: '',
      isValid: false,
      async handleSubmit() {
        if (! this.isValid) return

        if (
          await confirm(
            '{{ __('Are you absolutely sure? This action cannot be undone.') }}',
          )
        ) {
          $el.submit()
        }
      },
    }" @submit.prevent="handleSubmit">
      @csrf
      @method('delete')

      <label for="feedback" class="mt-4 block text-sm font-medium text-red-700 dark:text-red-400">
        {{ __('Please tell us why you are leaving (required)') }}
      </label>

      <div class="mt-1">
        <textarea id="feedback" name="feedback" rows="3" x-model="feedback" @input="isValid = feedback.length >= 3" class="block w-full rounded-md border p-2 border-red-300 bg-white text-red-900 shadow-xs focus:border-red-500 focus:ring-red-500 dark:border-red-700 dark:bg-gray-800 dark:text-red-100 dark:focus:border-red-500 dark:focus:ring-red-500 sm:text-sm" placeholder="{{ __('Your feedback helps us improve our service...') }}"></textarea>
      </div>

      <div class="mt-4 flex items-center justify-end gap-x-3">
        <button type="submit" x-bind:disabled="!isValid" x-bind:class="! isValid ? 'opacity-50 cursor-not-allowed' : ''" class="inline-flex items-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
          <x-phosphor-trash class="h-4 w-4" />
          {{ __('Delete my account') }}
        </button>
      </div>
    </form>
  </div>
</div>
