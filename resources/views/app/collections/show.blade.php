@use('App\Enums\ItemViewEnum')

@php
    $user = auth()->user();
    $canManage = $user->account->allowsManagementBy($user);

    $symbols = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥'];
    $symbol = $symbols[$collection->currency] ?? null;

    // Format an amount held in cents into the collection's currency.
    $money = function (int $cents) use ($symbol, $collection): string {
        $amount = number_format($cents / 100);

        if ($symbol !== null) {
            return $symbol.$amount;
        }

        return $collection->currency ? $amount.' '.$collection->currency : $amount;
    };

    // One display row per item, derived from its copies. Condition and location come from the
    // first copy; value is the sum across copies; quantity is the number of copies.
    $rows = $items->getCollection()->map(function ($item) use ($money) {
        $copies = $item->copies;
        $first = $copies->first();
        $valueCents = (int) $copies->sum('estimated_value');

        return [
            'id' => $item->id,
            'name' => $item->name,
            'categoryId' => (string) ($item->category_id ?? ''),
            'photoUrl' => $item->mainPhoto?->url(),
            'condition' => $first?->condition?->name ?? '—',
            'location' => $first?->location?->name ?? '—',
            'quantity' => $copies->count(),
            'value' => $valueCents > 0 ? $money($valueCents) : '—',
            'added' => $item->created_at->isoFormat('MMM D, YYYY'),
        ];
    });

    // Distinct locations used on this page, with counts, for the table's left filter pane.
    $locationFilters = $rows
        ->filter(fn (array $row): bool => $row['location'] !== '—')
        ->groupBy('location')
        ->map(fn ($group, string $label): array => ['label' => $label, 'count' => $group->count()])
        ->values();

    $totalValueLabel = $totalValue > 0 ? $money($totalValue) : '—';
@endphp

@if ($view === ItemViewEnum::Table)
    <x-app-layout>
        <x-slot:title>{{ $collection->name }}</x-slot>
        <x-slot:topNav>
            @include('app.collections.partials._top-bar')
        </x-slot>

        <div
            class="flex h-[calc(100vh-3.75rem)] flex-col overflow-hidden"
            x-data="{
                view: @js($view->value),
                serverView: @js($view->value),
                q: '',
                location: 'all',
                selectedId: {{ $rows->first()['id'] ?? 'null' }},
                columns: { condition: true, location: true, quantity: false, value: true, added: false },
                columnsOpen: false,
                items: @js($rows->values()),
                switchView(target) { switchCollectionView(this, target); },
                get selected() { return this.items.find(i => i.id === this.selectedId) ?? null; },
                rowVisible(name, loc) {
                    return (this.location === 'all' || this.location === loc)
                        && (this.q === '' || name.toLowerCase().includes(this.q.toLowerCase()));
                },
            }"
        >
            {{-- The view-switch endpoint for the current collection, read by switchCollectionView in app.js. --}}
            <input type="hidden" id="collection-view-endpoint" value="{{ route('collections.item-view.update', $collection) }}" />

            @include('app.collections.partials._table-header')
            @include('app.collections.partials._table')
        </div>
    </x-app-layout>
@else
    <x-app-layout :collection="$collection">
        <x-slot:title>{{ $collection->name }}</x-slot>

        <div
            class="px-6 py-8 lg:px-10"
            x-data="{
                view: @js($view->value),
                serverView: @js($view->value),
                q: '',
                category: 'all',
                switchView(target) { switchCollectionView(this, target); },
                cardVisible(name, cat) {
                    return (this.category === 'all' || this.category === cat)
                        && (this.q === '' || name.toLowerCase().includes(this.q.toLowerCase()));
                },
            }"
        >
            @include('app.collections.partials._header')

            @if ($items->isEmpty())
                <div class="mt-8 rounded-lg border border-hairline">
                    <x-empty-state>
                        <x-slot:icon>
                            <x-lucide-layers class="size-6 text-muted" />
                        </x-slot>
                        @if ($canManage)
                            {{ __('No items yet. Add your first one to start cataloging.') }}
                        @else
                            {{ __('No items yet.') }}
                        @endif
                    </x-empty-state>
                </div>
            @else
                @include('app.collections.partials._toolbar')

                {{-- Which view is visible on first paint is decided here, server side. Alpine
                     takes over the display once it boots, but until then x-show is inert, so
                     without this the page would paint the grid whatever the remembered view is. --}}
                <div x-show="view === '{{ ItemViewEnum::Grid->value }}'" @style(['display: none' => $view !== ItemViewEnum::Grid])>
                    @include('app.collections.partials._grid')
                </div>

                <div x-show="view === '{{ ItemViewEnum::List->value }}'" @style(['display: none' => $view !== ItemViewEnum::List])>
                    @include('app.collections.partials._list')
                </div>

                @include('app.collections.partials._pagination')
            @endif
        </div>
    </x-app-layout>
@endif
