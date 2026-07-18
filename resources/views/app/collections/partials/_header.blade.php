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
        <x-button href="{{ route('items.new', $collection) }}" turbo="true" data-test="new-item-button" class="shrink-0">
            <x-slot:icon>
                <x-lucide-plus class="size-4" />
            </x-slot>
            {{ __('Add item') }}
        </x-button>
    @endif
</div>
