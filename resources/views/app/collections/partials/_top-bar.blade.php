{{-- Table view chrome: the sidebar's navigation collapses into this full-width top bar. --}}
<header class="sticky top-0 z-30 flex h-15 items-center gap-4 overflow-x-auto border-b border-hairline bg-sidebar px-4 lg:px-6">
    <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex shrink-0 items-center gap-2">
        <x-logo size="22" aria-hidden="true" />
        <x-wordmark height="14" class="text-ink" />
    </a>

    <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex shrink-0 items-center gap-1.5 text-[13px] font-medium text-muted transition-colors hover:text-ink">
        @svg('lucide-arrow-left', 'size-4')
        <span>{{ __('Dashboard') }}</span>
    </a>

    <div class="h-5 w-px shrink-0 bg-hairline"></div>

    <span class="shrink-0 truncate text-sm font-semibold text-ink">{{ $collection->name }}</span>

    <nav class="flex shrink-0 items-center gap-1">
        <span class="rounded-md bg-card px-3 py-1.5 text-[13px] font-medium text-ink">{{ __('Items') }}</span>
        <a href="{{ route('sets.index', $collection) }}" data-turbo="true" class="rounded-md px-3 py-1.5 text-[13px] font-medium text-muted transition-colors hover:text-ink">{{ __('Sets') }}</a>
        <a href="{{ route('statistics.index', $collection) }}" data-turbo="true" class="rounded-md px-3 py-1.5 text-[13px] font-medium text-muted transition-colors hover:text-ink">{{ __('Statistics') }}</a>
        <a href="{{ route('categories.index', $collection) }}" data-turbo="true" class="rounded-md px-3 py-1.5 text-[13px] font-medium text-muted transition-colors hover:text-ink">{{ __('Manage categories') }}</a>
    </nav>

    <div class="flex-1"></div>

    <button
        type="button"
        @click="$store.theme.toggle()"
        class="flex size-8 shrink-0 items-center justify-center rounded-full border border-hairline bg-canvas text-muted transition-colors hover:text-ink"
        aria-label="{{ __('Toggle theme') }}"
    >
        <span class="hidden dark:block">@svg('lucide-sun', 'size-4 text-warning')</span>
        <span class="block dark:hidden">@svg('lucide-moon', 'size-4')</span>
    </button>

    <x-avatar-initials :name="auth()->user()->getFullName()" class="size-8 shrink-0 text-xs" />
</header>
