<?php
/**
 * @var \App\ViewModels\Settings\AccountIndexViewModel $view
 */
?>

<x-app-layout>
  <x-slot:title>
    {{ __('app/settings/account.title') }}
  </x-slot:title>

  <x-breadcrumb
    :items="[
    ['label' => __('app/breadcrumb.dashboard'), 'route' => $view->url()->dashboard],
    ['label' => __('app/breadcrumb.settings'), 'route' => $view->url()->settings],
    ['label' => __('app/breadcrumb.account')]
  ]" />

  <!-- settings layout -->
  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    <!-- Sidebar -->
    @include ('app.settings._sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-2xl space-y-6 sm:px-0">
        <!-- delete account -->
        @include ('app.settings.account._delete', ['view' => $view, 'errors' => $errors])
      </div>
    </section>
  </div>
</x-app-layout>
