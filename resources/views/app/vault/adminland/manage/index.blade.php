<x-app-layout :vault="$vault">
  <x-slot:title>
    {{ __('app/breadcrumb.manage_vault') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('app/breadcrumb.dashboard'), 'route' => route('vault.show', $vault)],
    ['label' => __('app/breadcrumb.adminland'), 'route' => route('vault.adminland.index', $vault)],
    ['label' => __('app/breadcrumb.manage_vault')]
  ]" />

  <!-- settings layout -->
  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    <!-- Sidebar -->
    @include('app.vault.adminland._sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-2xl space-y-6 sm:px-0">
        <!-- delete -->
        @include('app.vault.adminland.manage._destroy')
      </div>
    </section>
  </div>
</x-app-layout>
