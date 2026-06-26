<x-marketing-layout>
  @php
    $hasRightSidebar = isset($rightSidebar) && $rightSidebar->hasActualContent();
  @endphp

  @if (! empty($breadcrumbItems))
    <x-breadcrumb :items="$breadcrumbItems" />
  @endif

  <div class="relative mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
    <div @class (['grid grid-cols-1 gap-x-16', 'lg:grid-cols-[300px_1fr_250px]' => $hasRightSidebar, 'lg:grid-cols-[300px_1fr]' => ! $hasRightSidebar])>
      <!-- Sidebar -->
      <div class="hidden w-full shrink-0 flex-col justify-self-end sm:border-r sm:border-gray-200 sm:pr-3 lg:flex dark:sm:border-gray-700">
        <div class="bg-light dark:bg-dark z-10 pt-16">
          @if (request()->route('version'))
            <div class="mb-6">
              <p class="mb-2 text-xs tracking-widest text-gray-400 uppercase dark:text-gray-500">Version</p>
              <div class="flex gap-3">
                @foreach (config('docs.versions') as $v)
                  <a href="{{ route('marketing.docs.show', ['version' => $v, 'path' => request()->route('path') ?? '']) }}" class="{{ $currentVersion === $v ? 'font-semibold text-blue-600 dark:text-blue-400' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}">{{ $v }}</a>
                @endforeach
              </div>
            </div>
          @endif

          <x-marketing.docs-nav :items="$docNav" :version="$currentVersion" :depth="0" />
        </div>
      </div>

      <!-- Main content -->
      <div>{{ $slot }}</div>

      @if ($hasRightSidebar)
        <!-- Right sidebar -->
        <div class="hidden w-full shrink-0 flex-col justify-self-end py-16 sm:border-l sm:border-gray-200 sm:pl-6 lg:flex">{{ $rightSidebar }}</div>
      @endif
    </div>
  </div>
</x-marketing-layout>
