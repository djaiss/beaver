<x-app-layout>
  <x-slot:title>
    {{ __('Support tickets') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-4xl space-y-6">
      <div class="flex items-center gap-2">
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Support tickets') }}</h1>
        <x-soon />
      </div>

      <x-box padding="p-0">
        <x-empty-state>
          <x-slot:icon>
            @svg('lucide-message-square', 'size-5 text-muted')
          </x-slot>
          {{ __('Beaver does not handle support tickets yet. When it does, messages sent to support from across the instance will land here.') }}
        </x-empty-state>
      </x-box>
    </div>
  </div>
</x-app-layout>
