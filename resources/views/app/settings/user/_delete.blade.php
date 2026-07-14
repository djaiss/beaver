<div class="mb-2 flex items-center gap-x-2">
  <x-lucide-triangle-alert class="h-4 w-4 text-error" />
  <h2 class="mb-1 text-lg font-semibold text-error">
    {{ __('Delete your account') }}
  </h2>
</div>

<!-- Danger Zone -->
<div class="rounded-lg border border-error/30 bg-error/10">
  <div class="p-4">
    <p class="mb-4 text-sm text-error">
      {{ __('Your account and all data will be deleted immediately and cannot be restored. This is irreversible. Please be certain.') }}
    </p>

    <form action="{{ route('profile.user.destroy') }}" method="post" x-data="{
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

      <label for="feedback" class="mt-4 block text-sm font-medium text-error">
        {{ __('Please tell us why you are leaving (required)') }}
      </label>

      <div class="mt-1">
        <textarea id="feedback" name="feedback" rows="3" x-model="feedback" @input="isValid = feedback.length >= 3" class="block w-full rounded-md border border-error/40 bg-canvas p-2 text-ink shadow-xs focus:border-error focus:ring-error sm:text-sm" placeholder="{{ __('Your feedback helps us improve our service...') }}"></textarea>
      </div>

      <div class="mt-4 flex items-center justify-end gap-x-3">
        <button type="submit" x-bind:disabled="!isValid" x-bind:class="! isValid ? 'opacity-50 cursor-not-allowed' : ''" class="inline-flex items-center gap-x-2 rounded-md bg-error px-3.5 py-2 text-sm font-semibold text-white shadow-xs hover:bg-error/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-error">
          <x-lucide-trash-2 class="h-4 w-4" />
          {{ __('Delete my account') }}
        </button>
      </div>
    </form>
  </div>
</div>
