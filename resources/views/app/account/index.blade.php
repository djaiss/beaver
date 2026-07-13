<x-app-layout>
  <x-slot:title>
    {{ __('Accounts') }}
  </x-slot>

  <div class="px-6 pt-6">
    <div class="mx-auto w-full max-w-4xl items-start justify-center">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Your accounts') }}</h2>

        <x-button href="{{ route('accounts.new') }}" turbo="true">
          <x-slot:icon>
            <x-phosphor-plus-bold class="size-4" />
          </x-slot>
          {{ __('New account') }}
        </x-button>
      </div>

      <x-box padding="p-0">
        @forelse ($accounts as $account)
          <div class="rounded-0 flex items-center justify-between border-b border-gray-200 px-4 py-3 first:rounded-t-lg last:rounded-b-lg last:border-b-0 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
            <x-link href="{{ $account->link }}">{{ $account->name }}</x-link>

            <span class="text-sm text-gray-500">{{ __(ucfirst($account->role)) }}</span>
          </div>
        @empty
          <x-empty-state>
            <x-slot:icon>
              <x-phosphor-building-office class="size-6 text-gray-600" />
            </x-slot>

            {{ __('You are not a member of any account yet.') }}
          </x-empty-state>
        @endforelse
      </x-box>
    </div>
  </div>
</x-app-layout>
