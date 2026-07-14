<x-app-layout>
  <x-slot:title>
    {{ __('Security and access') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Security and access') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Manage your password, two-factor authentication and API keys.') }}</p>
      </div>

      <!-- user password -->
      @include('app.settings.security._password', ['errors' => $errors])

      <!-- two factor authentication -->
      @include('app.settings.security._2fa', ['has2fa' => $has2fa, 'errors' => $errors])

      <!-- auto delete account -->
      @include('app.settings.security._auto-delete', ['errors' => $errors])

      <!-- api keys -->
      @include('app.settings.security._api', ['apiKeys' => $apiKeys])
    </div>
  </div>
</x-app-layout>
