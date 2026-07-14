<x-app-layout>
  <x-slot:title>
    {{ $title }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <h1 class="mb-8 text-[28px] font-semibold tracking-tight text-ink">{{ $title }}</h1>

    <div class="rounded-lg border border-hairline p-12 text-center">
      <div class="mx-auto mb-4 flex size-12 items-center justify-center rounded-full bg-card">
        @svg('phosphor-package', 'size-6 text-muted')
      </div>
      <p class="text-base font-semibold text-ink">{{ __('Coming soon') }}</p>
      <p class="mx-auto mt-1 max-w-md text-sm text-muted">{{ $body }}</p>
    </div>
  </div>
</x-app-layout>
