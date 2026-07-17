@props(['collection' => null])

@php
    $user = auth()->user();
    $isProfile = request()->routeIs('profile.*');
    $isAccount = request()->routeIs('settings.*');
    $isCollection = $collection !== null;
@endphp

{{-- The closed position is a static class rather than an x-cloak plus :class pair. x-cloak
     would hide the sidebar until Alpine boots, and since it is in flow on desktop that drops
     it out of the layout and shifts the page once it appears. Alpine only removes
     -translate-x-full to slide it in on mobile. --}}
<aside
    data-morph-skip
    :class="{ '-translate-x-full': ! sidebarOpen }"
    class="fixed inset-y-0 left-0 z-40 flex w-60 shrink-0 -translate-x-full flex-col gap-6 border-r border-hairline bg-sidebar px-4 py-5 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
>
    {{-- Logo + theme toggle --}}
    <div class="flex items-center justify-between px-2">
        {{-- The mark is decorative here: the wordmark next to it already names the app,
             so labelling both would announce it twice. --}}
        <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex items-center gap-2">
            <x-logo size="26" aria-hidden="true" />
            <x-wordmark height="15" class="text-ink" />
        </a>

        <button
            type="button"
            @click="$store.theme.toggle()"
            class="flex size-8 items-center justify-center rounded-full border border-hairline bg-canvas text-muted transition-colors hover:text-ink"
            aria-label="{{ __('Toggle theme') }}"
        >
            {{-- Driven by the `dark` class the inline script in partials/meta sets before paint,
                 so the right icon is painted straight away instead of waiting on Alpine. --}}
            <span class="hidden dark:block">@svg('lucide-sun', 'size-4 text-warning')</span>
            <span class="block dark:hidden">@svg('lucide-moon', 'size-4')</span>
        </button>
    </div>

    @if ($isProfile)
        <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex items-center gap-2 px-2 text-[13px] font-medium text-muted transition-colors hover:text-ink">
            @svg('lucide-arrow-left', 'size-4')
            {{ __('Back to dashboard') }}
        </a>

        <nav class="flex flex-col gap-0.5">
            <p class="px-2 py-1.5 text-xs font-medium tracking-wide text-muted-soft uppercase">{{ __('Your profile') }}</p>
            <x-sidebar-link :href="route('profile.index')" :active="request()->routeIs('profile.index') || request()->routeIs('profile.logs.*') || request()->routeIs('profile.emails.*')" icon="user">{{ __('Profile') }}</x-sidebar-link>
            <x-sidebar-link :href="route('profile.security.index')" :active="request()->routeIs('profile.security.*')" icon="key">{{ __('Security & access') }}</x-sidebar-link>
            <x-sidebar-link :href="route('profile.webhooks.index')" :active="request()->routeIs('profile.webhooks.*')" icon="webhook">{{ __('Webhooks') }}</x-sidebar-link>
            <x-sidebar-link :href="route('profile.user.index')" :active="request()->routeIs('profile.user.*')" icon="triangle-alert">{{ __('Danger zone') }}</x-sidebar-link>
        </nav>
    @elseif ($isAccount)
        <a href="{{ route('dashboard.index') }}" data-turbo="true" class="flex items-center gap-2 px-2 text-[13px] font-medium text-muted transition-colors hover:text-ink">
            @svg('lucide-arrow-left', 'size-4')
            {{ __('Back to dashboard') }}
        </a>

        <nav class="flex flex-col gap-0.5">
            <p class="px-2 py-1.5 text-xs font-medium tracking-wide text-muted-soft uppercase">{{ __('Account') }}</p>
            @if ($user->isOwner())
                <x-sidebar-link :href="route('settings.index')" :active="request()->routeIs('settings.index')" icon="settings">{{ __('General') }}</x-sidebar-link>
                <x-sidebar-link :href="route('settings.members.index')" :active="request()->routeIs('settings.members.*')" icon="users">{{ __('Members') }}</x-sidebar-link>
            @endif
            <x-sidebar-link :href="route('settings.types.index')" :active="request()->routeIs('settings.types.*')" icon="boxes">{{ __('Collection types') }}</x-sidebar-link>
            <x-sidebar-link :href="route('settings.tags.index')" :active="request()->routeIs('settings.tags.*')" icon="tag">{{ __('Tags') }}</x-sidebar-link>
        </nav>
    @elseif ($isCollection)
        <a href="{{ route('collections.index') }}" data-turbo="true" class="flex items-center gap-2 px-2 text-[13px] font-medium text-muted transition-colors hover:text-ink">
            @svg('lucide-arrow-left', 'size-4')
            {{ __('Back to collections') }}
        </a>

        {{-- The sections below have no routes of their own yet, so they all point back at the
             collection. They get their own pages as each concept is built. --}}
        <nav class="flex flex-col gap-0.5">
            <p class="truncate px-2 py-1.5 text-xs font-medium tracking-wide text-muted-soft uppercase">{{ $collection->name }}</p>
            <x-sidebar-link :href="route('collections.show', $collection)" :active="true" color="bg-brand">{{ __('Items') }}</x-sidebar-link>
            <x-sidebar-link :href="route('collections.show', $collection)" :active="false" color="bg-badge-violet">{{ __('Categories') }}</x-sidebar-link>
            <x-sidebar-link :href="route('collections.show', $collection)" :active="false" color="bg-badge-emerald">{{ __('Sets') }}</x-sidebar-link>
            <x-sidebar-link :href="route('collections.show', $collection)" :active="false" color="bg-badge-orange">{{ __('Item details') }}</x-sidebar-link>
        </nav>
    @else
        <nav class="flex flex-col gap-0.5">
            <p class="px-2 py-1.5 text-xs font-medium tracking-wide text-muted-soft uppercase">{{ __('Workspace') }}</p>
            <x-sidebar-link :href="route('dashboard.index')" :active="request()->routeIs('dashboard.*')" icon="layout-grid">{{ __('Dashboard') }}</x-sidebar-link>
            <x-sidebar-link :href="route('search.index')" :active="request()->routeIs('search.*')" icon="search">{{ __('Search') }}</x-sidebar-link>
            <x-sidebar-link :href="route('collections.index')" :active="request()->routeIs('collections.*')" icon="layers">{{ __('Collections') }}</x-sidebar-link>
            <x-sidebar-link :href="route('locations.index')" :active="request()->routeIs('locations.*')" icon="map-pin">{{ __('Locations') }}</x-sidebar-link>
            @if ($user->isOwner())
                <x-sidebar-link :href="route('settings.index')" :active="false" icon="settings">{{ __('Account settings') }}</x-sidebar-link>
            @elseif ($user->account->allowsManagementBy($user))
                <x-sidebar-link :href="route('settings.types.index')" :active="false" icon="boxes">{{ __('Collection types') }}</x-sidebar-link>
            @endif
        </nav>
    @endif

    <div class="flex-1"></div>

    {{-- User block --}}
    <div x-data="{ open: false }" class="relative border-t border-hairline pt-3">
        <button type="button" @click="open = !open" class="cursor-pointer flex w-full items-center gap-2.5 rounded-md px-2 py-2 transition-colors hover:bg-canvas">
            <x-avatar-initials :name="$user->getFullName()" class="size-8 text-xs" />
            <span class="flex min-w-0 flex-1 flex-col text-left">
                <span class="truncate text-[13px] font-semibold text-ink">{{ $user->getFullName() }}</span>
                <span class="text-xs text-muted-soft capitalize">{{ __(ucfirst($user->role)) }}</span>
            </span>
            @svg('lucide-chevrons-up-down', 'size-4 shrink-0 text-muted-soft')
        </button>

        <div
            x-cloak
            x-show="open"
            @click.away="open = false"
            x-transition.opacity
            class="absolute bottom-full left-0 mb-1 w-full rounded-md border border-hairline bg-canvas p-1 shadow-md"
        >
            <a href="{{ route('profile.index') }}" data-turbo="true" class="cursor-pointer flex items-center gap-2 rounded px-2 py-1.5 text-sm text-body transition-colors hover:bg-card hover:text-ink">
                @svg('lucide-user', 'size-4 text-muted')
                {{ __('Profile') }}
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm text-body transition-colors hover:bg-card hover:text-ink">
                    @svg('lucide-log-out', 'size-4 text-muted')
                    {{ __('Logout') }}
                </button>
            </form>
        </div>
    </div>
</aside>
