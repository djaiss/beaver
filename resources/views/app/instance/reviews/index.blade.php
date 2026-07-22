<x-app-layout>
  <x-slot:title>
    User reviews
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-4xl space-y-6">
      <div class="flex items-center gap-2">
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">User reviews</h1>
        <x-soon />
      </div>

      <x-box padding="p-0">
        <x-empty-state>
          <x-slot:icon>
            @svg('lucide-star', 'size-5 text-muted')
          </x-slot>
          Beaver does not collect user reviews yet. When it does, you will moderate them here before they appear on the marketing site.
        </x-empty-state>
      </x-box>
    </div>
  </div>
</x-app-layout>
