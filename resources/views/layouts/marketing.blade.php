<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @include('partials.meta', ['title' => $title ?? null])

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/marketing.css', 'resources/js/marketing.js'])

    <!-- json-ld -->
    @yield('json-ld')
  </head>
  <body class="font-sans antialiased">
    <div class="min-h-screen bg-page text-ink">
      @include('components.marketing.header', ['announcement' => $announcement])

      <!-- Page Content -->
      <main>
        @if (! empty($breadcrumbItems))
          <x-breadcrumb :items="$breadcrumbItems" />
        @endif

        {{ $slot }}
      </main>

      @include('components.marketing.footer', ['footerColumns' => $footerColumns])
    </div>
  </body>
</html>
