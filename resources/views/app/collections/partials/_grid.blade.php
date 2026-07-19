<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($rows as $row)
        <a
            href="{{ route('items.show', [$collection, $row['id']]) }}"
            data-turbo="true"
            x-show="cardVisible(@js($row['name']), @js($row['categoryId']))"
            class="block overflow-hidden rounded-xl border border-hairline bg-canvas transition-colors hover:border-muted"
        >
            @if ($row['photoUrl'])
                <img src="{{ $row['photoUrl'] }}" alt="{{ $row['name'] }}" loading="lazy" class="h-36 w-full object-cover" />
            @else
                <div class="flex h-36 items-center justify-center bg-card text-3xl">
                    {{ $collection->emoji ?? '📦' }}
                </div>
            @endif
            <div class="p-3.5">
                <span class="mb-2 block truncate text-sm font-semibold text-ink">{{ $row['name'] }}</span>
                <div class="mb-2">
                    <x-badge>{{ $row['condition'] }}</x-badge>
                </div>
                <div class="flex items-center justify-between text-[13px] text-muted">
                    <span class="truncate">{{ $row['location'] }}</span>
                    <span class="shrink-0 font-semibold text-ink">{{ $row['value'] }}</span>
                </div>
            </div>
        </a>
    @endforeach
</div>
