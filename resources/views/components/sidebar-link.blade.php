@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    data-turbo="true"
    @click="sidebarOpen = false"
    {{ $attributes->class([
        'flex items-center gap-2.5 rounded-md px-2.5 py-2 text-sm font-medium transition-colors',
        'bg-canvas text-ink shadow-xs' => $active,
        'text-body hover:bg-canvas hover:text-ink' => ! $active,
    ]) }}
>
    @if ($icon)
        @svg('lucide-'.$icon, 'size-4 shrink-0 '.($active ? 'text-ink' : 'text-muted'))
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
