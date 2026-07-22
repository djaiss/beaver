@use('App\Helpers\Money')

@php
    $money = fn (int $cents): string => Money::format($cents, $collection->currency);
@endphp

<x-item-page :collection="$collection" :item="$item" :tags="$tags" tab="history">
  @include('app.items.partials._history')
</x-item-page>
