@if (auth()->guest())
  <x-marketing-layout :title="__('Page not found')">
    <section class="mx-auto flex max-w-[640px] flex-col items-center px-5 py-24 text-center sm:px-8 sm:py-32">
      <p class="mb-4 font-mono text-xs font-medium tracking-[1.4px] text-muted-soft uppercase">{{ __('Error 404') }}</p>

      <h1 class="text-[32px] leading-[1.05] font-semibold tracking-[-1px] text-balance text-ink sm:text-5xl">
        {{ __('This page went missing from the collection') }}
      </h1>

      <p class="mt-5 max-w-[480px] text-[17px] leading-relaxed text-muted">
        {{ __('We couldn’t find what you were looking for. It may have been moved, renamed, or deleted, or the link that brought you here is out of date.') }}
      </p>

      <p class="mt-2 font-mono text-sm text-muted-soft">/{{ request()->path() }}</p>

      <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('marketing.index') }}" data-turbo="true" class="flex h-12 items-center justify-center rounded-md bg-primary px-6 text-[15px] font-semibold text-on-primary transition-opacity hover:opacity-90">
          {{ __('Back to homepage') }}
        </a>

        <a href="{{ route('marketing.docs.portal.home.show') }}" data-turbo="true" class="flex h-12 items-center justify-center rounded-md border border-hairline px-6 text-[15px] font-semibold text-ink transition-colors hover:bg-sidebar">
          {{ __('Browse the docs') }}
        </a>
      </div>
    </section>
  </x-marketing-layout>
@else
  <x-errors.layout
    code="404"
    :name="__('Not found')"
    accent="#fb923c"
    accent-soft="#fdba74"
    :badge="__('no such page')"
    :headline="__('This page went missing from the collection')"
    :body="__('We couldn’t find what you were looking for. It may have been moved, renamed, or deleted, or the link that brought you here is out of date.')"
    :primary-label="__('Back to dashboard')"
    :primary-href="route('dashboard.index')"
    :secondary-label="__('Browse collections')"
    :secondary-href="route('collections.index')"
  >
    <x-slot:context>
      <x-errors.row :label="__('Requested path')" :value="'/'.request()->path()" mono />
    </x-slot:context>
  </x-errors.layout>
@endif
