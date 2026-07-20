@php
    $navigation = [
        ['label' => __('Features'), 'url' => route('marketing.index') . '#features'],
        ['label' => __('Pricing'), 'url' => route('marketing.pricing')],
        ['label' => __('Roadmap'), 'url' => route('marketing.index') . '#roadmap'],
        ['label' => __('API'), 'url' => route('marketing.docs.api.index')],
    ];
@endphp

<div x-data="{ mobileMenuOpen: false }">
  <div class="flex flex-col items-center justify-center gap-2 bg-[#101010] px-4 py-2 text-center text-[13px] font-medium sm:h-10 sm:flex-row sm:py-0">
    <div class="flex items-center gap-2">
      <span class="rounded-full bg-[#1a1a1a] px-2 py-[3px] text-[11px] font-semibold tracking-wide text-badge-emerald">v0.9</span>
      <span class="text-[#a1a1aa]">{{ __('Custom item types are here. Build a schema for any hobby.') }}</span>
    </div>
    <a href="{{ config('marketing.github_url') }}/releases" class="font-semibold text-white hover:underline">{{ __('Read the changelog') }} &rarr;</a>
  </div>

  {{-- Main nav --}}
  <div class="sticky top-0 z-50 border-b border-hairline bg-page/85 backdrop-blur-md">
    <nav class="mx-auto flex h-16 max-w-[1200px] items-center justify-between px-5 sm:px-8">
      <a href="{{ route('marketing.index') }}" class="group flex shrink-0 items-center gap-x-2.5">
        <div class="transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
          <x-logo size="30" aria-hidden="true" />
        </div>
        <x-wordmark height="17" class="text-ink" />
      </a>

      {{-- Desktop navigation --}}
      <div class="hidden items-center gap-x-1 lg:flex">
        @foreach ($navigation as $link)
          <a href="{{ $link['url'] }}" class="rounded-md px-3 py-2 text-sm font-medium text-body transition-colors hover:bg-sidebar hover:text-ink">{{ $link['label'] }}</a>
        @endforeach
      </div>

      <div class="flex items-center gap-x-2.5">
        <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="hidden items-center gap-x-2 rounded-md border border-hairline px-3.5 py-2 text-sm font-medium text-ink transition-colors hover:bg-sidebar sm:flex">
          <x-lucide-github class="h-4 w-4" />
          {{ __('GitHub') }}
        </a>

        @auth
          <a href="{{ route('dashboard.index') }}" class="flex h-10 items-center rounded-md bg-primary px-4 text-sm font-semibold text-on-primary transition-colors hover:opacity-90">{{ __('Go to your account') }}</a>
        @else
          <a href="{{ route('register') }}" class="flex h-10 items-center rounded-md bg-primary px-4.5 text-sm font-semibold text-on-primary transition-colors hover:opacity-90">{{ __('Get started') }}</a>
        @endauth

        {{-- Mobile menu button --}}
        <button type="button" @click="mobileMenuOpen = true" class="-mr-2 inline-flex items-center justify-center rounded-md p-2 text-ink lg:hidden">
          <span class="sr-only">{{ __('Open main menu') }}</span>
          <x-lucide-menu class="h-6 w-6" />
        </button>
      </div>
    </nav>
  </div>

  {{-- Mobile menu (off-canvas) --}}
  <div x-show="mobileMenuOpen" x-cloak class="lg:hidden">
    <div class="fixed inset-0 z-50 bg-ink/20" @click="mobileMenuOpen = false"></div>
    <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-page px-6 py-5 sm:max-w-sm sm:border-l sm:border-hairline">
      <div class="mb-6 flex items-center justify-between">
        <x-wordmark height="17" class="text-ink" />
        <button type="button" @click="mobileMenuOpen = false" class="-mr-2 rounded-md p-2 text-muted hover:bg-sidebar">
          <x-lucide-x class="h-6 w-6" />
          <span class="sr-only">{{ __('Close menu') }}</span>
        </button>
      </div>

      <div class="flex flex-col">
        @foreach ($navigation as $link)
          <a href="{{ $link['url'] }}" @click="mobileMenuOpen = false" class="border-b border-hairline-soft py-3.5 text-base font-semibold text-ink">{{ $link['label'] }}</a>
        @endforeach
        <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="border-b border-hairline-soft py-3.5 text-base font-semibold text-ink">{{ __('GitHub') }}</a>

        @guest
          <a href="{{ route('login') }}" class="py-3.5 text-base font-semibold text-ink">{{ __('Sign in') }}</a>
        @endguest
      </div>
    </div>
  </div>
</div>
