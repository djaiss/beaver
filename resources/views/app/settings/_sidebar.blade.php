<aside class="text-sm flex-col border-b border-gray-200 bg-white px-4 py-4 sm:flex sm:rounded-bl-lg sm:border-r sm:border-b-0 dark:border-gray-700 dark:bg-gray-900">
  <nav class="flex flex-col gap-1">
    <p class="mb-1 text-xs font-medium text-gray-500">{{ __('app/settings/sidebar.account') }}</p>
    <a data-turbo="true" href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index') ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} flex items-center gap-2 rounded-lg px-2 py-1">
      <x-phosphor-user class="h-4 w-4 {{ request()->routeIs('settings.index') ? 'text-emerald-700' : 'text-gray-500' }}" />
      {{ __('app/settings/sidebar.profile') }}
    </a>
    <a data-turbo="true" href="{{ route('settings.security.index') }}" class="{{ request()->routeIs('settings.security.index') ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} flex items-center gap-2 rounded-lg px-2 py-1 mb-4">
      <x-phosphor-key class="h-4 w-4 {{ request()->routeIs('settings.security.index') ? 'text-emerald-700' : 'text-gray-500' }}" />
      {{ __('app/settings/sidebar.security_and_access') }}
    </a>
    <p class="mb-1 text-xs font-medium text-gray-500">{{ __('app/settings/sidebar.administration') }}</p>
    <a data-turbo="true" href="{{ route('settings.account.index') }}" class="{{ request()->routeIs('settings.account.index') ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} flex items-center gap-2 rounded-lg px-2 py-1">
      <x-phosphor-gear class="h-4 w-4 {{ request()->routeIs('settings.index') ? 'text-emerald-700' : 'text-gray-500' }}" />
      {{ __('app/settings/sidebar.danger_zone') }}
    </a>
  </nav>
</aside>
