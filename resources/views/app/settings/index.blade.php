<x-app-layout>
  <x-slot:title>
    {{ __('Profile') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Profile') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Manage your personal details and account activity.') }}</p>
      </div>

      <!-- update user details -->
      @include('app.settings._detail', ['user' => $user, 'errors' => $errors])

      <!-- logs -->
      @include('app.settings._logs', ['logs' => $logs, 'hasMoreLogs' => $hasMoreLogs])

      <!-- emails sent -->
      @include('app.settings._emails', ['emails' => $emails, 'hasMoreEmails' => $hasMoreEmails])
    </div>
  </div>
</x-app-layout>
