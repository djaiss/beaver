@props(['locale'])

@php
    $language = config('docs.locales.'.$locale.'.label', $locale);
@endphp

<div x-data="{ shown: true }" x-show="shown" x-cloak class="border-b border-amber-200 bg-amber-50">
  <div class="mx-auto flex max-w-[1440px] items-center gap-3 px-7 py-3">
    <x-lucide-triangle-alert class="h-[18px] w-[18px] shrink-0 text-amber-700" />
    <p class="flex-1 text-sm leading-relaxed text-amber-800">
      <strong class="font-semibold text-amber-900">{{ __('No :language version yet.', ['language' => $language]) }}</strong>
      {{ __('This page has not been translated into :language, so the English version is shown instead.', ['language' => $language]) }}
    </p>
    <button type="button" @click="shown = false" class="shrink-0 p-1 text-amber-700 hover:text-amber-900">
      <x-lucide-x class="h-4 w-4" />
      <span class="sr-only">{{ __('Dismiss') }}</span>
    </button>
  </div>
</div>
