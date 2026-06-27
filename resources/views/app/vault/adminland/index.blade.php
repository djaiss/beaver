<x-app-layout :vault="$vault">
  <x-slot:title>
    {{ __('Preferences') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('vault.show', $vault)],
    ['label' => __('Adminland')],
  ]" />

  <!-- settings layout -->
  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950 rounded-bl-lg rounded-br-lg">
    <!-- Sidebar -->
    @include('app.vault.adminland._sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-2xl space-y-6 sm:px-0">
        <!-- edit vault -->
        @include('app.vault.adminland._edit')

        <!-- genders -->
        @include('app.vault.adminland._genders')

        <!-- relationship types -->
        @include('app.vault.adminland._relationship-types')
      </div>
    </section>
  </div>
</x-app-layout>
