<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @include('partials.meta', ['title' => config('app.name').' API documentation'])

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      html {
        scroll-behavior: smooth;
      }
    </style>
  </head>
  <body class="bg-white font-sans text-gray-900 antialiased">
    <div x-data="{ query: '' }">
      <x-api-docs.topbar />

      <div class="flex">
        <x-api-docs.sidebar :navigation="$navigation" />

        <main class="min-w-0 flex-1">
          @foreach ($sections as $section)
            <x-api-docs.section :section="$section" />
          @endforeach

          <footer class="bg-neutral-950 px-8 py-14 text-center">
            <p class="mb-2 text-base font-bold text-white">{{ strtolower(config('app.name')) }}</p>
            <p class="text-[13px] text-zinc-400">Built for people who keep real inventories.</p>
          </footer>
        </main>
      </div>
    </div>
  </body>
</html>
