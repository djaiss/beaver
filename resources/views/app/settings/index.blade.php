<x-app-layout>
  <x-slot:title>
    {{ __('app/settings/settings.title') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('app/breadcrumb.dashboard'), 'route' => route('vault.index')],
    ['label' => __('app/breadcrumb.settings')]
  ]" />

  <!-- settings layout -->
  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    <!-- Sidebar -->
    @include('app.settings._sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-4xl flex-col gap-y-8 sm:px-0">
        <!-- update user details -->
        @include('app.settings._detail', ['user' => $user, 'errors' => $errors])

        <!-- logs -->
        @include('app.settings._logs', ['logs' => $logs, 'hasMoreLogs' => $hasMoreLogs])

        <!-- emails sent -->
        @include('app.settings._emails', ['emails' => $emails, 'hasMoreEmails' => $hasMoreEmails])
      </div>
    </section>
  </div>
</x-app-layout>
