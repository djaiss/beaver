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
        @forelse ($catalogs as $catalog)
          <a href="{{ route('collections.show', $catalog->id) }}" data-turbo="true" data-test="collection-row-{{ $catalog->id }}" class="flex items-center gap-5 rounded-xl border border-hairline bg-canvas p-5 transition-colors hover:bg-card">
            <div class="flex size-16 shrink-0 items-center justify-center rounded-xl border border-hairline bg-card text-3xl">{{ $catalog->emoji ?? '📦' }}</div>

            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2.5">
                <h2 class="truncate text-lg font-semibold text-ink">{{ $catalog->name }}</h2>
                <x-badge>{{ __(ucfirst($catalog->visibility->value)) }}</x-badge>
              </div>
              @if ($catalog->description)
                <p class="mt-1 truncate text-sm text-muted">{{ $catalog->description }}</p>
              @endif
            </div>

            <div class="flex shrink-0 items-center gap-4">
              <x-avatar :user="$catalog->createdBy" :name="$catalog->created_by_name ?? '?'" :size="32" class="size-8 text-xs" />
              <span class="w-20 text-right text-xs text-muted-soft">{{ $catalog->updated_at?->diffForHumans() }}</span>
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
