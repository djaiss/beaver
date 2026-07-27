<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @php
        $seo = app(\App\Services\MarketingSeo::class)->forRequest(request(), $pageTitle ?? null, $pageDescription ?? null);
    @endphp

    @include('partials.meta', ['title' => $seo['title'], 'description' => $seo['description']])
    @include('partials.marketingMeta', ['seo' => $seo])

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/marketing.css', 'resources/js/marketing.js'])

    <!-- json-ld -->
    @yield('json-ld')
  </head>
  <body class="font-sans antialiased">
    <div class="min-h-screen bg-page text-ink">
      @include('components.marketing.header')

      <!-- Page Content -->
      <main>
        @if (! empty($breadcrumbItems))
          <x-breadcrumb :items="$breadcrumbItems" />
        @endif

        {{ $slot }}
      </main>

      @include('components.marketing.footer')
    </div>
  </body>
</html>
