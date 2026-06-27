<x-app-layout>
  <x-slot:title>
    {{ __('Account administration') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('vault.index')],
    ['label' => __('Settings'), 'route' => route('settings.index')],
    ['label' => __('Account administration')]
  ]" />

  <!-- settings layout -->
  <div class="grid flex-grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    <!-- Sidebar -->
    @include('app.settings._sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-2xl space-y-6 sm:px-0">
        <!-- delete account -->
        @include('app.settings.account._delete', ['errors' => $errors])
      </div>
    </section>
  </div>
</x-app-layout>
