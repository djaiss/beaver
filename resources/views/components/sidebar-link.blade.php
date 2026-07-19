{{-- `color` paints the square swatch of a collection nav entry, `dot` the round one of a
     category, and `count` the number sitting at the far right of a category. --}}
@props(['href', 'active' => false, 'icon' => null, 'color' => null, 'dot' => null, 'count' => null])

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
    @if ($dot)
        <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $dot }}"></span>
    @elseif ($color)
        <span class="size-4 shrink-0 rounded-[5px] {{ $color }}"></span>
    @elseif ($icon)
        @svg('lucide-'.$icon, 'size-4 shrink-0 '.($active ? 'text-ink' : 'text-muted'))
    @endif
    <span class="flex-1 truncate">{{ $slot }}</span>
    @if ($count !== null)
        <span class="shrink-0 text-xs text-muted-soft">{{ number_format($count) }}</span>
    @endif
</a>
