<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($rows as $row)
        <div
            x-show="cardVisible(@js($row['name']), @js($row['categoryId']))"
            class="overflow-hidden rounded-xl border border-hairline bg-canvas"
        >
            <div class="flex h-36 items-center justify-center bg-card text-3xl">
                {{ $collection->emoji ?? '📦' }}
            </div>
            <div class="p-3.5">
                <p class="mb-2 truncate text-sm font-semibold text-ink">{{ $row['name'] }}</p>
                <div class="mb-2">
                    <x-badge>{{ $row['condition'] }}</x-badge>
                </div>
                <div class="flex items-center justify-between text-[13px] text-muted">
                    <span class="truncate">{{ $row['location'] }}</span>
                    <span class="shrink-0 font-semibold text-ink">{{ $row['value'] }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
