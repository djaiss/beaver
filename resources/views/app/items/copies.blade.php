@use('App\Helpers\Money')

@php
    $money = fn (int $cents): string => Money::format($cents, $catalog->currency);

    // A copy carries no value of its own any more, so the total is the sum of
    // what each one was last valued at. A copy nobody has valued adds nothing.
    $totalEstimated = (int) $item->copies->sum(fn ($copy): int => $copy->estimatedValue() ?? 0);
@endphp

<x-item-page :catalog="$catalog" :item="$item" :tags="$tags" tab="copies">
  @include('app.items.partials._copies')
</x-item-page>
