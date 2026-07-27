<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @php
        $seo = app(\App\ViewModels\MarketingSeo::class)->forRequest(request(), $pageTitle ?? null, $pageDescription ?? null);
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
