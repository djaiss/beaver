<x-app-layout :collection="$collection">
  <x-slot:title>
    {{ $collection->name }}
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-2xl space-y-8">
      <div class="flex items-center gap-1.5 text-[13px]">
        <a href="{{ route('collections.index') }}" data-turbo="true" class="font-medium text-muted-soft transition-colors hover:text-ink">{{ __('Collections') }}</a>
        <span class="text-muted-soft">/</span>
        <span class="font-medium text-ink">{{ $collection->name }}</span>
      </div>

      <div class="flex items-start gap-5">
        <div class="flex size-16 shrink-0 items-center justify-center rounded-xl border border-hairline bg-card text-3xl">{{ $collection->emoji ?? '📦' }}</div>

        <div class="min-w-0 flex-1">
          <h1 class="truncate text-[28px] font-semibold tracking-tight text-ink">{{ $collection->name }}</h1>
          @if ($collection->description)
            <p class="mt-1.5 text-[15px] text-muted">{{ $collection->description }}</p>
          @endif
        </div>

        @if (auth()->user()->account->allowsManagementBy(auth()->user()))
          <x-button href="{{ route('items.new', $collection) }}" turbo="true" data-test="new-item-button">
            <x-slot:icon>
              <x-lucide-plus class="size-4" />
            </x-slot>
            {{ __('Add item') }}
          </x-button>
        @endif
      </div>

      <div class="flex flex-wrap gap-2">
        @foreach ($collection->collectionTypes as $type)
          <span class="flex items-center gap-2 rounded-full border border-hairline px-3.5 py-2 text-sm font-medium text-ink">
            <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $type->color }}"></span>
            {{ $type->name }}
          </span>
        @endforeach
      </div>

      <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
        <div class="rounded-lg bg-card p-4">
          <p class="text-[13px] font-medium text-muted">{{ __('Visibility') }}</p>
          <p class="mt-1 text-sm font-semibold text-ink capitalize">{{ $collection->visibility->value }}</p>
        </div>

        <div class="rounded-lg bg-card p-4">
          <p class="text-[13px] font-medium text-muted">{{ __('Currency') }}</p>
          <p class="mt-1 text-sm font-semibold text-ink">{{ $collection->currency ?? __('None') }}</p>
        </div>
      </div>

      <div class="rounded-lg border border-hairline">
        <x-empty-state>
          <x-slot:icon>
            <x-lucide-layers class="size-6 text-muted" />
          </x-slot>
          @if (auth()->user()->account->allowsManagementBy(auth()->user()))
            {{ __('No items yet. Add your first one to start cataloging.') }}
          @else
            {{ __('No items yet.') }}
          @endif
        </x-empty-state>
      </div>
    </div>
  </div>
</x-app-layout>
