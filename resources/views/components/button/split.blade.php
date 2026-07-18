{{--
  A two part button: a primary action on the left, and a chevron on the right
  that opens a menu of secondary actions. Attributes land on the primary
  action, so it takes an href, an onclick or a form= like any other button.
  The default slot holds the menu, usually made of x-menu-item.
--}}
@props([
  'label',
  'href' => null,
  'type' => 'button',
  'turbo' => false,
  'menuLabel' => null,
])

@php
  $base = 'relative inline-flex h-10 cursor-pointer items-center justify-center text-sm font-semibold whitespace-nowrap transition-colors duration-150 focus-visible:z-10 focus-visible:ring-2 focus-visible:outline-none disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 dark:disabled:opacity-75';
  $visual = 'border border-black/10 bg-[var(--color-accent)] text-[var(--color-accent-foreground)] shadow-[inset_0px_1px_--theme(--color-white/.2)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] focus-visible:ring-[var(--color-accent)]/50 dark:border-0';
  $actionClasses = $base . ' ' . $visual . ' gap-2 rounded-l-md border-r-0 [:where(&)]:px-5';
  $toggleClasses = $base . ' ' . $visual . ' w-9 rounded-r-md border-l-0';
@endphp

<div x-data="{ open: false }" class="relative inline-block">
  <div class="inline-flex items-stretch">
    @isset($href)
      <a href="{{ $href }}" {{ $attributes->merge(['class' => $actionClasses]) }} @if($turbo) data-turbo="true" @endif>
        @isset($icon)
          <span class="shrink-0">{{ $icon }}</span>
        @endisset

        {{ $label }}
      </a>
    @else
      <button type="{{ $type }}" {{ $attributes->merge(['class' => $actionClasses]) }}>
        @isset($icon)
          <span class="shrink-0">{{ $icon }}</span>
        @endisset

        {{ $label }}
      </button>
    @endisset

    {{-- A drawn divider rather than a border: the accent visual drops borders in dark mode. --}}
    <span aria-hidden="true" class="w-px self-stretch bg-black/20 dark:bg-white/25"></span>

    <button
      type="button"
      @click="open = ! open"
      :aria-expanded="open ? 'true' : 'false'"
      aria-haspopup="menu"
      aria-label="{{ $menuLabel ?? __('More actions') }}"
      class="{{ $toggleClasses }}"
    >
      @svg('lucide-chevron-down', 'size-3.5 shrink-0 transition-transform duration-150', ['x-bind:class' => "open && 'rotate-180'"])
    </button>
  </div>

  <div
    x-cloak
    x-show="open"
    @click.away="open = false"
    @keydown.escape.window="open = false"
    x-transition.opacity
    role="menu"
    class="absolute top-full right-0 z-40 mt-1.5 w-56 rounded-lg border border-hairline bg-canvas p-1.5 shadow-lg"
  >
    {{ $slot }}
  </div>
</div>
