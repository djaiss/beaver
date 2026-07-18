<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @include('partials.meta', ['title' => $title ?? null])

    {{ $head ?? '' }}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen bg-page font-sans text-body antialiased">
    {{-- When a page supplies a `topNav` slot (e.g. the collection detail's table view), the
         chrome flips: the left sidebar is dropped and the slot renders as a full-width top bar. --}}
    <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
      @empty($topNav)
        {{-- Mobile backdrop --}}
        <div
          x-cloak
          data-morph-skip
          x-show="sidebarOpen"
          x-transition.opacity
          @click="sidebarOpen = false"
          class="fixed inset-0 z-30 bg-black/40 lg:hidden"
        ></div>

        <x-sidebar :collection="$collection" />
      @endempty

      <div class="flex min-w-0 flex-1 flex-col">
        @isset($topNav)
          {{ $topNav }}
        @else
          {{-- Mobile top bar --}}
          <div class="flex items-center gap-3 border-b border-hairline bg-page px-4 py-3 lg:hidden">
            <button
              type="button"
              @click="sidebarOpen = true"
              class="flex size-9 items-center justify-center rounded-md border border-hairline bg-canvas text-muted"
              aria-label="{{ __('Open menu') }}"
            >
              @svg('lucide-list', 'size-5')
            </button>
            <x-wordmark height="14" class="text-ink" />
          </div>
        @endisset

        <main class="min-w-0 flex-1">
          {{ $slot }}
        </main>
      </div>
    </div>

    <x-toaster />
  </body>
</html>
