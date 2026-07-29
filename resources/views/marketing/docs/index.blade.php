<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @php
        $seo = app(\App\ViewModels\MarketingSeo::class)->forRequest(request());
        $structuredData = app(\App\ViewModels\MarketingStructuredData::class)->forRequest(request());
    @endphp

    @include('partials.meta', ['title' => $seo['title'], 'description' => $seo['description']])
    @include('partials.marketingMeta', ['seo' => $seo, 'structuredData' => $structuredData])

    @vite(['resources/css/marketing.css', 'resources/js/marketing.js'])

    <style>
      html {
        scroll-behavior: smooth;
      }
    </style>
  </head>
  <body class="bg-white font-sans text-gray-900 antialiased">
    <div x-data="{ query: '' }">
      @include('components.marketing.header')

      <x-api-docs.subheader />

      <div class="flex">
        <x-api-docs.sidebar :navigation="$navigation" />

        <main id="main-content" tabindex="-1" class="min-w-0 flex-1 scroll-mt-20 focus:outline-none">
          @foreach ($sections as $section)
            <x-api-docs.section :section="$section" />
          @endforeach

          <footer class="bg-neutral-950 px-8 py-14 text-center">
            <x-wordmark height="14" class="mx-auto mb-2 block text-white" />
            <p class="text-[13px] text-zinc-400">Built for people who keep real inventories.</p>
          </footer>
        </main>
      </div>
    </div>
  </body>
</html>
