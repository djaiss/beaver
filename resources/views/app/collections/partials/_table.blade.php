@php
    $columns = [
        ['key' => 'condition', 'label' => __('Condition')],
        ['key' => 'location', 'label' => __('Location')],
        ['key' => 'quantity', 'label' => __('Quantity')],
        ['key' => 'value', 'label' => __('Value')],
        ['key' => 'added', 'label' => __('Added')],
    ];
@endphp

<div class="flex h-[calc(100vh-360px)] min-h-[420px] overflow-hidden rounded-xl border border-hairline">

    {{-- LEFT: location filters --}}
    <div class="w-48 shrink-0 overflow-y-auto border-r border-hairline bg-sidebar">
        <p class="border-b border-hairline-soft px-4 py-3.5 text-xs font-semibold tracking-wide text-muted-soft uppercase">{{ __('Filter by location') }}</p>
        <button
            type="button"
            @click="location = 'all'"
            :class="location === 'all' ? 'bg-card text-ink' : 'text-muted hover:text-ink'"
            class="flex w-full cursor-pointer items-center justify-between px-4 py-2.5 text-left text-[13px] font-medium transition-colors"
        >
            <span>{{ __('All') }}</span>
            <span class="text-xs text-muted-soft">{{ $rows->count() }}</span>
        </button>
        @foreach ($locationFilters as $filter)
            <button
                type="button"
                @click="location = @js($filter['label'])"
                :class="location === @js($filter['label']) ? 'bg-card text-ink' : 'text-muted hover:text-ink'"
                class="flex w-full cursor-pointer items-center justify-between px-4 py-2.5 text-left text-[13px] font-medium transition-colors"
            >
                <span class="truncate">{{ $filter['label'] }}</span>
                <span class="ml-2 shrink-0 text-xs text-muted-soft">{{ $filter['count'] }}</span>
            </button>
        @endforeach
    </div>

    {{-- MIDDLE: table --}}
    <div class="relative flex min-w-0 flex-1 flex-col">
        <div class="relative flex shrink-0 justify-end border-b border-hairline-soft p-2">
            <button
                type="button"
                @click="columnsOpen = !columnsOpen"
                class="cursor-pointer rounded-md border border-hairline px-3 py-1.5 text-[13px] font-medium text-ink hover:bg-card"
            >{{ __('Columns') }}</button>
            <div
                x-show="columnsOpen"
                x-cloak
                @click.outside="columnsOpen = false"
                x-transition.opacity
                class="absolute top-11 right-2 z-10 flex flex-col gap-0.5 rounded-lg border border-hairline bg-canvas p-1.5 shadow-md"
            >
                @foreach ($columns as $column)
                    <label class="flex cursor-pointer items-center gap-2 rounded px-2.5 py-1.5 text-[13px] text-ink hover:bg-card">
                        <input type="checkbox" x-model="columns.{{ $column['key'] }}" class="rounded border-hairline text-[var(--color-accent)] focus:ring-[var(--color-accent)]/40" />
                        <span class="whitespace-nowrap">{{ $column['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex-1 overflow-auto">
            <table class="w-max min-w-full border-collapse">
                <thead>
                    <tr>
                        <th class="sticky top-0 z-[1] min-w-56 border-b border-hairline bg-canvas px-4 py-2.5 text-left text-xs font-semibold tracking-wide text-muted-soft uppercase">{{ __('Name') }}</th>
                        @foreach ($columns as $column)
                            <th x-show="columns.{{ $column['key'] }}" x-cloak class="sticky top-0 z-[1] min-w-36 border-b border-hairline bg-canvas px-4 py-2.5 text-left text-xs font-semibold tracking-wide text-muted-soft uppercase">{{ $column['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr
                            x-show="rowVisible(@js($row['name']), @js($row['location']))"
                            @click="selectedId = {{ $row['id'] }}"
                            :class="selectedId === {{ $row['id'] }} ? 'bg-card' : 'hover:bg-card/60'"
                            class="cursor-pointer"
                        >
                            <td class="border-b border-hairline-soft px-4 py-2.5 text-sm font-semibold whitespace-nowrap text-ink">{{ $row['name'] }}</td>
                            <td x-show="columns.condition" x-cloak class="border-b border-hairline-soft px-4 py-2.5 text-[13px] whitespace-nowrap text-muted">{{ $row['condition'] }}</td>
                            <td x-show="columns.location" x-cloak class="border-b border-hairline-soft px-4 py-2.5 text-[13px] whitespace-nowrap text-muted">{{ $row['location'] }}</td>
                            <td x-show="columns.quantity" x-cloak class="border-b border-hairline-soft px-4 py-2.5 text-[13px] whitespace-nowrap text-muted">{{ $row['quantity'] }}</td>
                            <td x-show="columns.value" x-cloak class="border-b border-hairline-soft px-4 py-2.5 text-[13px] font-semibold whitespace-nowrap text-ink">{{ $row['value'] }}</td>
                            <td x-show="columns.added" x-cloak class="border-b border-hairline-soft px-4 py-2.5 text-[13px] whitespace-nowrap text-muted">{{ $row['added'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT: selected item details --}}
    <div class="w-72 shrink-0 overflow-y-auto border-l border-hairline p-5">
        <template x-if="selected">
            <div>
                <img x-show="selected.photoUrl" :src="selected.photoUrl" :alt="selected.name" class="mb-4 aspect-[3/4] w-full rounded-lg object-cover" />
                <div x-show="! selected.photoUrl" class="mb-4 flex aspect-[3/4] items-center justify-center rounded-lg bg-card text-4xl">{{ $collection->emoji ?? '📦' }}</div>
                <p class="mb-4 text-base font-semibold text-ink" x-text="selected.name"></p>
                <dl class="flex flex-col">
                    <div class="flex justify-between border-b border-hairline-soft py-2.5">
                        <dt class="text-[13px] text-muted-soft">{{ __('Condition') }}</dt>
                        <dd class="text-[13px] font-semibold text-ink" x-text="selected.condition"></dd>
                    </div>
                    <div class="flex justify-between border-b border-hairline-soft py-2.5">
                        <dt class="text-[13px] text-muted-soft">{{ __('Location') }}</dt>
                        <dd class="text-[13px] font-semibold text-ink" x-text="selected.location"></dd>
                    </div>
                    <div class="flex justify-between border-b border-hairline-soft py-2.5">
                        <dt class="text-[13px] text-muted-soft">{{ __('Quantity') }}</dt>
                        <dd class="text-[13px] font-semibold text-ink" x-text="selected.quantity"></dd>
                    </div>
                    <div class="flex justify-between border-b border-hairline-soft py-2.5">
                        <dt class="text-[13px] text-muted-soft">{{ __('Value') }}</dt>
                        <dd class="text-[13px] font-semibold text-ink" x-text="selected.value"></dd>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <dt class="text-[13px] text-muted-soft">{{ __('Added') }}</dt>
                        <dd class="text-[13px] font-semibold text-ink" x-text="selected.added"></dd>
                    </div>
                </dl>
            </div>
        </template>
        <template x-if="! selected">
            <p class="text-[13px] text-muted">{{ __('Select an item to see its details.') }}</p>
        </template>
    </div>
</div>
