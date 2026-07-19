<x-app-layout>
  <x-slot:title>
    {{ __('Collections') }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-4xl">
      <div class="mb-8 flex items-start justify-between gap-4">
        <div>
          <h1 class="text-[28px] font-semibold tracking-tight text-ink">{{ __('Collections') }}</h1>
          <p class="mt-1 text-[15px] text-muted">{{ __('Everything shared across your account.') }}</p>
        </div>

        <x-button href="{{ route('collections.new') }}" turbo="true">
          <x-slot:icon>
            <x-lucide-plus class="size-4" />
          </x-slot>
          {{ __('New collection') }}
        </x-button>
      </div>

      <div class="space-y-3">
        @forelse ($collections as $collection)
          <a href="{{ route('collections.show', $collection->id) }}" data-turbo="true" data-test="collection-row-{{ $collection->id }}" class="flex items-center gap-5 rounded-xl border border-hairline bg-canvas p-5 transition-colors hover:bg-card">
            <div class="flex size-16 shrink-0 items-center justify-center rounded-xl border border-hairline bg-card text-3xl">{{ $collection->emoji ?? '📦' }}</div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2.5">
                <h2 class="truncate text-lg font-semibold text-ink">{{ $collection->name }}</h2>
                <x-badge>{{ __(ucfirst($collection->visibility->value)) }}</x-badge>
              </div>
              @if ($collection->description)
                <p class="mt-1 truncate text-sm text-muted">{{ $collection->description }}</p>
              @endif
            </div>

            <div class="flex shrink-0 items-center gap-4">
              <x-avatar :user="$collection->createdBy" :name="$collection->created_by_name ?? '?'" :size="32" class="size-8 text-xs" />
              <span class="w-20 text-right text-xs text-muted-soft">{{ $collection->updated_at?->diffForHumans() }}</span>
            </div>
          </a>
        @empty
          <div class="rounded-lg border border-hairline">
            <x-empty-state>
              <x-slot:icon>
                <x-lucide-layers class="size-6 text-muted" />
              </x-slot>
              {{ __('No collections yet. Create your first one to get started.') }}
            </x-empty-state>
          </div>
        @endforelse
      </div>
    </div>
  </div>
</x-app-layout>
