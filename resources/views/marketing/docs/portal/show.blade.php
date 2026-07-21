@php
    $sectionTitle = collect($navigation)
        ->first(fn (array $section): bool => collect($section['items'])->contains('id', $page['id']))['title'] ?? null;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $locale) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @include('partials.meta', ['title' => $page['title'].' — '.config('app.name').' '.__('documentation')])

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/marketing.css', 'resources/js/marketing.js'])

    <style>
      html { scroll-behavior: smooth; }
      [x-cloak] { display: none !important; }
      .doc-scroll::-webkit-scrollbar { width: 8px; }
      .doc-scroll::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 8px; }
    </style>
  </head>
  <body class="font-sans antialiased">
    <div class="min-h-screen bg-white text-gray-900">
      @include('components.marketing.header')

      <x-docs.portal-subheader :locale="$locale" :urlLocale="$urlLocale" :languageUrls="$languageUrls" />

      @if ($fallback)
        <x-docs.portal-fallback-banner :locale="$locale" />
      @endif

      <div class="mx-auto flex max-w-[1440px]">
        <x-docs.portal-sidebar :navigation="$navigation" :locale="$locale" :currentId="$page['id']" />

        {{-- Center: content --}}
        <main id="doc-top" class="min-w-0 flex-1 px-6 py-10 sm:px-10 lg:px-16 lg:pb-24">
          <div class="mx-auto max-w-[720px]">
            {{-- Breadcrumb --}}
            <nav class="mb-5 flex items-center gap-2.5 text-sm text-gray-500">
              <a href="{{ route('marketing.docs.portal.home.show', ['locale' => $urlLocale]) }}" class="hover:text-gray-900">{{ __('Home') }}</a>
              @if ($sectionTitle)
                <span class="text-gray-300">/</span>
                <span>{{ $sectionTitle }}</span>
              @endif
            </nav>

            <h1 class="mb-4 text-3xl font-bold tracking-tight text-gray-900">{{ $page['title'] }}</h1>

            {{-- Content actions (copy for LLM / view as Markdown are out of scope for now). --}}
            <div class="mb-3.5 flex flex-wrap items-center gap-0 text-sm font-medium text-gray-600">
              <button type="button" class="flex items-center gap-2 py-1 pr-4.5 hover:text-blue-600">
                <x-lucide-clipboard class="h-[15px] w-[15px]" />
                {{ __('Copy for LLM') }}
              </button>
              <span class="h-4 w-px bg-gray-200"></span>
              <button type="button" class="flex items-center gap-2 py-1 px-4.5 hover:text-blue-600">
                <x-lucide-file-text class="h-[15px] w-[15px]" />
                {{ __('View as Markdown') }}
              </button>
            </div>

            <div class="mb-7 h-px bg-gray-100"></div>

            {{-- Rendered Markdown --}}
            <div class="doc-content prose prose-gray max-w-none prose-headings:font-semibold prose-headings:tracking-tight prose-a:font-medium prose-a:text-blue-600 prose-a:no-underline hover:prose-a:underline prose-code:rounded prose-code:bg-gray-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:font-normal prose-code:before:content-none prose-code:after:content-none prose-pre:rounded-xl prose-pre:border prose-pre:border-gray-200 prose-pre:bg-gray-50 prose-pre:text-gray-800 prose-img:rounded-xl prose-img:border prose-img:border-gray-200">
              {!! $content !!}
            </div>

            {{-- Edit link (static). --}}
            <div class="mt-11 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-6">
              <span class="text-[13px] text-gray-400">{{ __('Documentation for :name', ['name' => config('app.name')]) }}</span>
              <a href="{{ config('marketing.github_url') }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-[13px] font-medium text-gray-500 hover:text-gray-900">
                <x-lucide-github class="h-[15px] w-[15px]" />
                {{ __('Edit this page on GitHub') }}
              </a>
            </div>

            {{-- Was this page useful (out of scope for now, shown for parity with the design). --}}
            <div class="mt-6 rounded-xl border border-gray-200 p-5">
              <div class="flex flex-wrap items-center gap-3.5">
                <span class="text-[15px] font-semibold text-gray-900">{{ __('Was this page useful?') }}</span>
                <div class="flex gap-2">
                  <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400"><x-lucide-frown class="h-5 w-5" /></span>
                  <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400"><x-lucide-meh class="h-5 w-5" /></span>
                  <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400"><x-lucide-smile class="h-5 w-5" /></span>
                </div>
              </div>
            </div>
          </div>
        </main>

        <x-docs.portal-toc :toc="$toc" />
      </div>

      @include('components.marketing.footer')
    </div>

    <script>
      // Highlight the table of contents entry for the heading currently in view.
      // Registered on turbo:load so it re-arms after every Turbo navigation.
      document.addEventListener('turbo:load', () => {
        const links = Array.from(document.querySelectorAll('#doc-toc [data-toc]'));

        if (links.length === 0) {
          return;
        }

        const activeClasses = ['border-blue-600', 'font-semibold', 'text-gray-900'];
        const headings = links
          .map((link) => document.getElementById(link.dataset.toc))
          .filter(Boolean);

        const setActive = (id) => {
          links.forEach((link) => {
            link.classList.toggle('border-blue-600', link.dataset.toc === id);
            link.classList.toggle('font-semibold', link.dataset.toc === id);
            link.classList.toggle('text-gray-900', link.dataset.toc === id);
          });
        };

        const observer = new IntersectionObserver(
          (entries) => {
            const visible = entries.filter((entry) => entry.isIntersecting);

            if (visible.length > 0) {
              setActive(visible[0].target.id);
            }
          },
          { rootMargin: '-140px 0px -70% 0px' },
        );

        headings.forEach((heading) => observer.observe(heading));
      });
    </script>
  </body>
</html>
