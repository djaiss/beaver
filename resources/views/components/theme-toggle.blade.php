{{--
  Light/dark switch, shared by the logged in sidebar and the marketing footer.

  Which icon shows is driven by the `dark` class that the inline script in partials/meta puts
  on <html> before paint, rather than by Alpine, so the right one is painted straight away.
  That script falls back to the operating system preference when the visitor has never chosen
  a theme, which is what makes this button start on whatever the OS already asked for.
--}}
<button
    type="button"
    x-data
    @click="$store.theme.toggle()"
    aria-label="{{ __('Toggle theme') }}"
    data-test="theme-toggle"
    {{ $attributes->class(['flex size-8 items-center justify-center rounded-full transition-colors']) }}
>
    <span class="hidden dark:block">@svg('lucide-sun', 'size-4 text-warning')</span>
    <span class="block dark:hidden">@svg('lucide-moon', 'size-4')</span>
</button>
