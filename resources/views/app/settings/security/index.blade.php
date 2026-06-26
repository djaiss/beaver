<?php
/**
 * @var \App\ViewModels\Settings\SecurityIndexViewModel $view
 */
?>

<x-app-layout>
  <x-slot:title>
    {{ __('app/settings/security.title') }}
  </x-slot:title>

  <x-breadcrumb
    :items="[
    ['label' => __('app/breadcrumb.dashboard'), 'route' => route('vault.index')],
    ['label' => __('app/breadcrumb.security_and_access')],
  ]" />

  <!-- settings layout -->
  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    <!-- Sidebar -->
    @include ('app.settings._sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-2xl space-y-6 sm:px-0">
        <!-- user password -->
        @include ('app.settings.security._password', ['view' => $view, 'errors' => $errors])

        <!-- two factor authentication -->
        @include ('app.settings.security._2fa', ['view' => $view, 'errors' => $errors])

        <!-- auto delete account -->
        @include ('app.settings.security._auto-delete', ['view' => $view, 'errors' => $errors])

        <!-- api keys -->
        @include ('app.settings.security._api', ['view' => $view, 'errors' => $errors])
      </div>
    </section>
  </div>
</x-app-layout>
