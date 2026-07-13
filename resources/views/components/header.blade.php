<header {{ $attributes->class(['flex w-full max-w-[1920px] items-center px-2 sm:pr-4 sm:pl-9']) }}>
  <!-- normal desktop header -->
  <nav class="hidden flex-1 items-center gap-3 pt-2 pb-2 sm:flex">
    <a href="/" class="flex items-center">
      <x-image src="{{ asset('images/marketing/logo/30x30.webp') }}" srcset="{{ asset('images/marketing/logo/30x30.webp') }} 1x, {{ asset('images/marketing/logo/30x30@2x.webp') }} 2x" width="25" height="25" alt="{{ config('app.name') }} logo" />
    </a>

    <!-- separator -->
    <div class="-ml-4 flex-1"></div>

    <!-- right side menu -->
    <div class="flex items-center gap-1">
      <a class="flex items-center gap-2 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100 dark:hover:border-gray-700 dark:hover:bg-gray-800" href="" data-turbo="true">
        <x-phosphor-books class="size-4 text-gray-600 transition-transform duration-150" />
        {{ __('Modules') }}
      </a>

      <a href="" class="flex items-center gap-2 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100 dark:hover:border-gray-700 dark:hover:bg-gray-800">
        <x-phosphor-lifebuoy class="size-4 text-gray-600 transition-transform duration-150" />
        {{ __('Docs') }}
      </a>

      <div x-data="{ menuOpen: false }" @click.away="menuOpen = false" class="relative">
        <button @click="menuOpen = !menuOpen" :class="{ 'bg-gray-100 dark:bg-gray-800' : menuOpen }" class="flex cursor-pointer items-center gap-1 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100 dark:hover:border-gray-700 dark:hover:bg-gray-800">
          {{ __('Menu') }}
          <x-phosphor-caret-down class="size-4 text-gray-600 transition-transform duration-150" x-bind:class="{ 'rotate-180' : menuOpen }" />
        </button>

        <div x-cloak x-show="menuOpen" x-transition:enter="transition duration-50 ease-linear" x-transition:enter-start="-translate-y-1 opacity-90" x-transition:enter-end="translate-y-0 opacity-100" class="absolute top-0 right-0 z-50 mt-10 w-56 min-w-32 rounded-md border border-gray-200/70 bg-white p-1 text-sm text-gray-800 shadow-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" x-cloak>
          <a @click="menuOpen = false" href="" class="relative flex w-full cursor-pointer items-center rounded px-2 py-1.5 outline-none select-none hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100">
            <x-phosphor-user class="mr-2 size-4 text-gray-600" />
            {{ __('Instance administration') }}
          </a>

          <div class="-mx-1 my-1 h-px bg-gray-200 dark:bg-gray-700"></div>

          <a @click="menuOpen = false" href="{{ route('accounts.index') }}" class="relative flex w-full cursor-pointer items-center rounded px-2 py-1.5 outline-none select-none hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100">
            <x-phosphor-building-office class="mr-2 size-4 text-gray-600" />
            {{ __('Accounts') }}
          </a>

          <div class="-mx-1 my-1 h-px bg-gray-200 dark:bg-gray-700"></div>

          <a @click="menuOpen = false" href="{{ route('settings.index') }}" class="relative flex w-full cursor-pointer items-center rounded px-2 py-1.5 outline-none select-none hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100">
            <x-phosphor-user class="mr-2 size-4 text-gray-600" />
            {{ __('Profile') }}
          </a>

          <div class="-mx-1 my-1 h-px bg-gray-200 dark:bg-gray-700"></div>

          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button @click="menuOpen = false" type="submit" class="relative flex w-full cursor-pointer items-center rounded px-2 py-1.5 outline-none select-none hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-100">
              <x-phosphor-sign-out class="mr-2 size-4 text-gray-600" />
              {{ __('Logout') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </nav>
</header>
