@use('App\Helpers\Money')

@php
    $money = fn (int $cents): string => Money::format($cents, $collection->currency);

    $totalEstimated = (int) $item->copies->sum('estimated_value');
@endphp

<x-item-page :collection="$collection" :item="$item" :tags="$tags" tab="copies">
  @include('app.items.partials._copies')
</x-item-page>
