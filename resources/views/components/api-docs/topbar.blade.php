<header class="sticky top-0 z-50 flex h-16 items-center gap-6 border-b border-gray-200 bg-white px-4 sm:px-8">
  <div class="flex shrink-0 items-center gap-2">
    <a href="{{ route('marketing.index') }}" class="flex items-center text-gray-900">
      <x-wordmark height="13" />
    </a>
    <span class="text-gray-400">/</span>
    <span class="text-base font-semibold text-gray-900">API</span>
    <span class="ml-2 rounded-full bg-gray-100 px-2.5 py-1 font-mono text-xs font-semibold text-gray-700">v1</span>
  </div>

  <div class="hidden max-w-sm flex-1 items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 md:flex">
    <x-lucide-search class="h-4 w-4 shrink-0 text-gray-400" />
    <input
      x-model="query"
      x-ref="search"
      @keydown.window.prevent.meta.k="$refs.search.focus()"
      @keydown.window.prevent.ctrl.k="$refs.search.focus()"
      type="search"
      placeholder="Search endpoints"
      class="w-full border-0 bg-transparent p-0 text-sm text-gray-900 placeholder-gray-400 focus:ring-0 focus:outline-none"
    />
    <kbd class="rounded bg-gray-200 px-1.5 py-0.5 font-mono text-[11px] text-gray-500">⌘K</kbd>
  </div>

  <div class="flex-1"></div>

  <div class="flex shrink-0 items-center gap-4 sm:gap-6">
    <a href="#introduction" class="hidden text-sm font-medium text-gray-500 hover:text-gray-900 sm:block">Guides</a>
    <a href="{{ route('marketing.docs.markdown.index') }}" class="hidden text-sm font-medium text-gray-500 hover:text-gray-900 sm:block">Markdown</a>
    <a href="{{ route('profile.api-keys.new') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-900 hover:border-gray-300">Get API key</a>
  </div>
</header>
