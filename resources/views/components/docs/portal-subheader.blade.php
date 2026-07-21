@props(['locale', 'languageUrls'])

@php
    $current = collect($languageUrls)->firstWhere('current', true);
@endphp

<div class="sticky top-16 z-40 border-b border-gray-200 bg-white">
  <div class="mx-auto flex h-14 max-w-[1440px] items-center gap-4 px-5 sm:px-8">
    <a href="{{ route('marketing.docs.portal.home.show', ['locale' => $locale]) }}" class="flex shrink-0 items-center gap-2 text-sm font-semibold text-gray-900">
      <x-lucide-book-open class="h-4 w-4" />
      {{ __('Documentation') }}
    </a>

    {{-- Static search box (search is out of scope for now). --}}
    <div class="hidden flex-1 cursor-text items-center gap-2 rounded-lg border border-gray-100 bg-gray-50 px-3 py-[7px] text-gray-400 sm:flex sm:max-w-md">
      <x-lucide-search class="h-3.5 w-3.5" />
      <span class="flex-1 text-sm">{{ __('Search documentation') }}</span>
      <span class="rounded bg-gray-200 px-1.5 py-0.5 font-mono text-[11px] text-gray-500">&#8984;K</span>
    </div>

    <div class="flex-1 sm:hidden"></div>

    {{-- Language selector: mirrors the site language choice, keyed on the page id. --}}
    <div x-data="{ open: false }" class="relative shrink-0" @click.outside="open = false">
      <button
        type="button"
        @click="open = !open"
        class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-2.5 py-[7px] text-[13px] font-semibold text-gray-900 hover:bg-gray-50"
      >
        <x-lucide-globe class="h-[15px] w-[15px] text-gray-500" />
        {{ $current['code'] ?? strtoupper($locale) }}
        <x-lucide-chevron-down class="h-3.5 w-3.5 text-gray-400" />
      </button>

      <div
        x-show="open"
        x-cloak
        class="absolute top-11 right-0 z-50 w-56 rounded-xl border border-gray-200 bg-white p-1.5 shadow-xl"
      >
        @foreach ($languageUrls as $language)
          <a
            href="{{ $language['url'] }}"
            class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium text-gray-900 hover:bg-gray-50 {{ $language['current'] ? 'bg-gray-50' : '' }}"
          >
            <span class="w-6 font-mono text-[11px] font-semibold text-gray-500">{{ $language['code'] }}</span>
            <span class="flex-1">{{ $language['label'] }}</span>
            @if ($language['current'])
              <x-lucide-check class="h-[15px] w-[15px] text-blue-600" />
            @elseif (! $language['translated'])
              <span class="text-[11px] font-medium text-amber-700">{{ __('no version') }}</span>
            @endif
          </a>
        @endforeach
      </div>
    </div>
  </div>
</div>
