@use('App\Helpers\Money')

@php
    $money = fn (int $cents): string => Money::format($cents, $catalog->currency);
@endphp

<x-item-page :catalog="$catalog" :item="$item" :tags="$tags" tab="history">
  @include('app.items.partials._history')
</x-item-page>
