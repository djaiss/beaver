{{-- The view-switch endpoint for the current collection, read by switchCollectionView in app.js. --}}
<input type="hidden" id="collection-view-endpoint" value="{{ route('collections.item-view.update', $collection) }}" />

<div class="mb-6 flex items-center gap-1.5 text-[13px]">
    <a href="{{ route('collections.index') }}" data-turbo="true" class="font-medium text-muted-soft transition-colors hover:text-ink">{{ __('Collections') }}</a>
    <span class="text-muted-soft">/</span>
    <span class="truncate font-medium text-ink">{{ $collection->name }}</span>
</div>

<div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-start">
    <div class="flex size-24 shrink-0 items-center justify-center rounded-2xl border border-hairline bg-card text-5xl">{{ $collection->emoji ?? '📦' }}</div>

    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2.5">
            <h1 class="text-[28px] font-semibold tracking-tight text-ink">{{ $collection->name }}</h1>
            <x-badge>{{ __(ucfirst($collection->visibility->value)) }}</x-badge>
        </div>

        @if ($collection->description)
            <p class="mt-1.5 max-w-xl text-[15px] leading-relaxed text-muted">{{ $collection->description }}</p>
        @endif

        <div class="mt-4 flex flex-wrap gap-6">
            <div>
                <p class="text-[13px] text-muted-soft">{{ __('Items') }}</p>
                <p class="text-base font-semibold text-ink">{{ number_format($itemCount) }}</p>
            </div>
            <div>
                <p class="text-[13px] text-muted-soft">{{ __('Est. value') }}</p>
                <p class="text-base font-semibold text-ink">{{ $totalValueLabel }}</p>
            </div>
            <div>
                <p class="text-[13px] text-muted-soft">{{ __('Currency') }}</p>
                <p class="text-base font-semibold text-ink">{{ $collection->currency ?? __('None') }}</p>
            </div>
        </div>
    </div>

    @if ($canManage)
        <div class="shrink-0">
            {{-- The delete button sits inside the menu, so its form lives outside and is reached with form=. --}}
            <x-form method="delete" :action="route('collections.destroy', $collection->id)" id="delete-collection-form" data-turbo="true" class="hidden" onsubmit="return confirm('{{ __('Delete :name? The collection and everything in it will no longer be accessible.', ['name' => $collection->name]) }}')"></x-form>

            <x-button.split :href="route('items.new', $collection)" :label="__('Add item')" turbo data-test="new-item-button">
                <x-slot:icon>
                    <x-lucide-plus class="size-4" />
                </x-slot>

                <x-menu-item :href="route('collections.edit', $collection->id)" turbo data-test="edit-collection-button">
                    <x-slot:icon>@svg('lucide-pencil', 'size-4 text-muted')</x-slot>
                    {{ __('Edit collection') }}
                </x-menu-item>

                <div class="my-1 h-px bg-hairline"></div>

                <x-menu-item type="submit" form="delete-collection-form" danger data-test="delete-collection-button">
                    <x-slot:icon>@svg('lucide-trash-2', 'size-4')</x-slot>
                    {{ __('Delete collection') }}
                </x-menu-item>
            </x-button.split>
        </div>
    @endif
</div>
