<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @include('partials.meta', ['title' => $title ?? null])

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans text-body antialiased bg-page">
    <div class="flex min-h-screen flex-col items-center bg-page pt-6 sm:justify-center sm:pt-0">
      <div>
        {{ $slot }}
      </div>
    </div>
  </body>
</html>
