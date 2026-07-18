<div class="overflow-hidden rounded-xl border border-hairline">
    @foreach ($rows as $row)
        <div
            x-show="cardVisible(@js($row['name']), @js($row['categoryId']))"
            class="flex items-center gap-3.5 border-b border-hairline-soft px-4 py-3 last:border-b-0"
        >
            <div class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-card text-lg">{{ $collection->emoji ?? '📦' }}</div>
            <p class="min-w-0 flex-1 truncate text-sm font-semibold text-ink">{{ $row['name'] }}</p>
            <div class="shrink-0">
                <x-badge>{{ $row['condition'] }}</x-badge>
            </div>
            <div class="hidden w-28 shrink-0 truncate text-[13px] text-muted sm:block">{{ $row['location'] }}</div>
            <div class="w-20 shrink-0 text-right text-[13px] font-semibold text-ink">{{ $row['value'] }}</div>
        </div>
    @endforeach
</div>
