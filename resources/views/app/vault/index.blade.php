<x-app-layout>
  <x-slot:title>
    {{ __('Dashboard') }}
  </x-slot>

  <div class="px-6 pt-6">
    <div class="mx-auto w-full max-w-4xl items-start justify-center">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Your vaults') }}</h2>

        <div class="flex items-center gap-x-2">
          <x-button.secondary href="{{ route('vault.join.create') }}" turbo="true">
            {{ __('Join') }}
          </x-button.secondary>

          <x-button href="{{ route('vault.create') }}" turbo="true">
            <x-slot:icon>
              <x-phosphor-plus-bold class="size-4" />
            </x-slot>
            {{ __('New vault') }}
          </x-button>
        </div>
      </div>

      <x-box padding="p-0">
        @forelse ($vaults as $vault)
          <div class="rounded-0 flex items-center justify-between border-b border-gray-200 first:rounded-t-lg last:rounded-b-lg last:border-b-0 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <div class="flex items-center">
              <div class="mr-2 rounded-full p-3">
                <img src="{{ $vault->avatar }}" class="h-8 w-8" alt="Avatar" />
              </div>

              <x-link href="{{ $vault->link }}">{{ $vault->name }}</x-link>
            </div>
          </div>
        @empty
          <x-empty-state>
            <x-slot:icon>
              <x-phosphor-building-office class="size-6 text-gray-600" />
            </x-slot>

            {{ __('You are not a member of any vaults yet.') }}
          </x-empty-state>
        @endforelse
      </x-box>
    </div>
  </div>
</x-app-layout>
