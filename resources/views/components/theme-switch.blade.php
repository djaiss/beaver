{{--
  The light/dark switch used in the marketing footer: a sun, a track with a knob, and a moon.

  It sits on the footer's near black background in both themes, so its colours are written
  as literals rather than as theme tokens. Which side the knob rests on is driven by the
  `dark` class the inline script in partials/meta puts on <html> before paint, so it is
  painted in the right position straight away instead of jumping once Alpine boots.
--}}
<button
    type="button"
    x-data
    @click="$store.theme.toggle()"
    :aria-pressed="$store.theme.dark"
    aria-label="{{ __('Toggle theme') }}"
    data-test="theme-switch"
    {{ $attributes->class(['group flex items-center gap-x-2.5']) }}
>
    @svg('lucide-sun', 'h-[15px] w-[15px] text-[#f0f0f0] dark:text-[#777777]')

    <span class="relative h-6 w-11 rounded-full border border-[#2f2f2f] bg-[#242424] transition-colors dark:bg-[#3a3a3a]">
        <span class="absolute top-0.5 left-0.5 h-[18px] w-[18px] rounded-full bg-[#eeeeee] shadow transition-transform duration-200 dark:translate-x-[22px]"></span>
    </span>

    @svg('lucide-moon', 'h-[15px] w-[15px] text-[#777777] dark:text-[#f0f0f0]')
</button>
