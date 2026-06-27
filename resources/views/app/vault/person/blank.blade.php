<x-app-layout :vault="$vault">
  <div class="flex h-[calc(100vh-48px)] flex-col items-center justify-center bg-gray-50 px-4 text-center sm:px-6 lg:px-8">
    <x-box class="max-w-md m.x-auto">
      <div class="space-y-4">
        <!-- Text content -->
        <h3 class="text-2xl font-semibold text-gray-900">
          {{ __('Welcome to :name', ['name' => config('app.name')]) }}
        </h3>

        <p class="text-base text-gray-600">
          {{ __('Add your first contact to document your relationships.') }}
        </p>

        <!-- Call to action -->
        <a href="{{ route('vault.person.new', ['vaultId' => $vault->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-xs hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-hidden" data-turbo="true">
          <x-phosphor-plus class="h-5 w-5" />
          {{ __('Add your first person') }}
        </a>

        <!-- Help text -->
        <p class="text-sm text-gray-500">
          {{ __('You can add family members, friends, colleagues, or anyone else you want to keep in touch with.') }}
        </p>
      </div>
    </x-box>
  </div>
</x-app-layout>
