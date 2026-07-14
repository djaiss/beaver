@php
    $user = auth()->user();
    $isProfile = request()->routeIs('profile.*');
    $isAccount = request()->routeIs('settings.*');
@endphp

<aside
    x-cloak
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-40 flex w-60 shrink-0 flex-col gap-6 border-r border-hairline bg-sidebar px-4 py-5 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
>
    {{-- Logo + theme toggle --}}
    <div class="flex items-center justify-between px-2">
        <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex items-center gap-2">
            <span class="size-[26px] shrink-0 rounded-full bg-ink"></span>
            <span class="text-[17px] font-semibold tracking-tight text-ink">{{ config('app.name') }}</span>
        </a>

        <button
            type="button"
            @click="$store.theme.toggle()"
            class="flex size-8 items-center justify-center rounded-full border border-hairline bg-canvas text-muted transition-colors hover:text-ink"
            aria-label="{{ __('Toggle theme') }}"
        >
            <span x-cloak x-show="$store.theme.dark">@svg('phosphor-sun', 'size-4 text-warning')</span>
            <span x-cloak x-show="!$store.theme.dark">@svg('phosphor-moon', 'size-4')</span>
        </button>
    </div>

    @if ($isProfile)
        <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex items-center gap-2 px-2 text-[13px] font-medium text-muted transition-colors hover:text-ink">
            @svg('phosphor-arrow-left', 'size-4')
            {{ __('Back to dashboard') }}
        </a>

        <nav class="flex flex-col gap-0.5">
            <p class="px-2 py-1.5 text-xs font-medium tracking-wide text-muted-soft uppercase">{{ __('Your profile') }}</p>
            <x-sidebar-link :href="route('profile.index')" :active="request()->routeIs('profile.index') || request()->routeIs('profile.logs.*') || request()->routeIs('profile.emails.*')" icon="user">{{ __('Profile') }}</x-sidebar-link>
            <x-sidebar-link :href="route('profile.security.index')" :active="request()->routeIs('profile.security.*')" icon="key">{{ __('Security & access') }}</x-sidebar-link>
            <x-sidebar-link :href="route('profile.webhooks.index')" :active="request()->routeIs('profile.webhooks.*')" icon="webhooks-logo">{{ __('Webhooks') }}</x-sidebar-link>
            <x-sidebar-link :href="route('profile.user.index')" :active="request()->routeIs('profile.user.*')" icon="warning">{{ __('Danger zone') }}</x-sidebar-link>
        </nav>
    @elseif ($isAccount)
        <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex items-center gap-2 px-2 text-[13px] font-medium text-muted transition-colors hover:text-ink">
            @svg('phosphor-arrow-left', 'size-4')
            {{ __('Back to dashboard') }}
        </a>

        <nav class="flex flex-col gap-0.5">
            <p class="px-2 py-1.5 text-xs font-medium tracking-wide text-muted-soft uppercase">{{ __('Account') }}</p>
            <x-sidebar-link :href="route('settings.index')" :active="request()->routeIs('settings.index')" icon="gear">{{ __('General') }}</x-sidebar-link>
            <x-sidebar-link :href="route('settings.members.index')" :active="request()->routeIs('settings.members.*')" icon="users">{{ __('Members') }}</x-sidebar-link>
        </nav>
    @else
        <nav class="flex flex-col gap-0.5">
            <p class="px-2 py-1.5 text-xs font-medium tracking-wide text-muted-soft uppercase">{{ __('Workspace') }}</p>
            <x-sidebar-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')" icon="squares-four">{{ __('Dashboard') }}</x-sidebar-link>
            <x-sidebar-link :href="route('search.index')" :active="request()->routeIs('search.*')" icon="magnifying-glass">{{ __('Search') }}</x-sidebar-link>
            <x-sidebar-link :href="route('collections.index')" :active="request()->routeIs('collections.*')" icon="stack">{{ __('Collections') }}</x-sidebar-link>
            <x-sidebar-link :href="route('locations.index')" :active="request()->routeIs('locations.*')" icon="map-pin">{{ __('Locations') }}</x-sidebar-link>
            @if ($user->isOwner())
                <x-sidebar-link :href="route('settings.index')" :active="false" icon="gear">{{ __('Account settings') }}</x-sidebar-link>
            @endif
        </nav>
    @endif

    <div class="flex-1"></div>

    {{-- User block --}}
    <div x-data="{ open: false }" class="relative border-t border-hairline pt-3">
        <button type="button" @click="open = !open" class="flex w-full items-center gap-2.5 rounded-md px-2 py-2 transition-colors hover:bg-canvas">
            <x-avatar-initials :name="$user->getFullName()" class="size-8 text-xs" />
            <span class="flex min-w-0 flex-1 flex-col text-left">
                <span class="truncate text-[13px] font-semibold text-ink">{{ $user->getFullName() }}</span>
                <span class="text-xs text-muted-soft capitalize">{{ __(ucfirst($user->role)) }}</span>
            </span>
            @svg('phosphor-caret-up-down', 'size-4 shrink-0 text-muted-soft')
        </button>

        <div
            x-cloak
            x-show="open"
            @click.away="open = false"
            x-transition.opacity
            class="absolute bottom-full left-0 mb-1 w-full rounded-md border border-hairline bg-canvas p-1 shadow-md"
        >
            <a href="{{ route('profile.index') }}" data-turbo="true" class="flex items-center gap-2 rounded px-2 py-1.5 text-sm text-body transition-colors hover:bg-card hover:text-ink">
                @svg('phosphor-user', 'size-4 text-muted')
                {{ __('Profile') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded px-2 py-1.5 text-sm text-body transition-colors hover:bg-card hover:text-ink">
                    @svg('phosphor-sign-out', 'size-4 text-muted')
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </div>
</aside>
