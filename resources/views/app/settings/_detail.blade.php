<?php
/**
 * @var \App\ViewModels\Settings\SettingsIndexViewModel $view
 */
?>

<x-box>
  <x-slot:title>
    {{ __('app/settings/settings.details.title') }}
  </x-slot:title>

  <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
    <div class="space-y-2">
      <p class="text-sm text-gray-500">{{ __('app/settings/settings.details.description') }}</p>
      <p class="text-sm text-gray-500">{{ __('app/settings/settings.details.nickname_description') }}</p>
      <p class="text-sm text-gray-500">{{ __('app/settings/settings.details.email_change_description') }}</p>
    </div>

    <x-form method="put" :action="$view->url()->profileUpdate" class="space-y-4">
      <!-- First name -->
      <x-input id="first_name" value="{{ old('first_name', $view->user()->first_name) }}" :label="__('app/settings/settings.details.first_name')" required placeholder="John" :error="$errors->get('first_name')" autofocus />

      <!-- Last name -->
      <x-input id="last_name" value="{{ old('last_name', $view->user()->last_name) }}" :label="__('app/settings/settings.details.last_name')" required placeholder="Doe" :error="$errors->get('last_name')" />

      <!-- nickname -->
      <x-input id="nickname" value="{{ old('nickname', $view->user()?->nickname) }}" :label="__('app/settings/settings.details.nickname')" :error="$errors->get('nickname')" />

      <!-- email -->
      <x-input id="email" value="{{ old('email', $view->user()->email) }}" :label="__('app/settings/settings.details.email')" required placeholder="john@doe.com" :error="$errors->get('email')" />

      <!-- locale -->
      <x-select id="locale" :label="__('app/settings/settings.details.language')" :options="['en' => __('app/settings/settings.details.english'), 'fr_FR' => __('app/settings/settings.details.french'), 'es_ES' => __('app/settings/settings.details.spanish'), 'de_DE' => __('app/settings/settings.details.german')]" selected="{{ $view->user()->locale }}" required :error="$errors->get('locale')" />

      <!-- time format -->
      <x-select id="time_format_24h" :label="__('app/settings/settings.details.time_format')" :options="['true' => __('app/settings/settings.details.format_24h'), 'false' => __('app/settings/settings.details.format_12h')]" selected="{{ $view->user()->time_format_24h ? 'true' : 'false' }}" required :error="$errors->get('time_format_24h')" />

      <div class="flex items-center justify-end">
        <x-button>{{ __('app/shared.save') }}</x-button>
      </div>
    </x-form>
  </div>
</x-box>
