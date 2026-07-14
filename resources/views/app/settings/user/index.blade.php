<x-app-layout>
  <x-slot:title>
    {{ __('Account administration') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Danger zone') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Irreversible actions for your account.') }}</p>
      </div>

      <!-- delete account -->
      @include('app.settings.user._delete', ['errors' => $errors])
    </div>
  </div>
</x-app-layout>
