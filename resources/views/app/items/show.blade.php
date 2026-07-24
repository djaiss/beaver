@use('App\Enums\FieldTypeEnum')
@use('App\Helpers\Money')

@php
    $type = $item->catalogType;
    $values = $item->customFieldValues->keyBy('custom_field_id');

    // A field reads as its stored value, except for a yes/no field, whose
    // underlying "1" means nothing to a reader.
    $display = function ($field) use ($values): ?string {
        $value = $values->get($field->id)?->value;

        if ($value === null || $value === '') {
            return null;
        }

        if ($field->field_type === FieldTypeEnum::Boolean) {
            return $value === '1' ? __('Yes') : __('No');
        }

        return $value;
    };

    $money = fn (int $cents): string => Money::format($cents, $catalog->currency);

    // A copy carries no value of its own any more, so the total is the sum of
    // what each one was last valued at.
    $totalEstimated = (int) $item->copies->sum(fn ($copy): int => $copy->estimatedValue() ?? 0);

    $ungroupedFields = $type?->ungroupedCustomFields ?? collect();
    $fieldGroups = $type?->customFieldGroups ?? collect();
    $hasDetails = $ungroupedFields->isNotEmpty() || $fieldGroups->isNotEmpty();
@endphp

<x-item-page :catalog="$catalog" :item="$item" :tags="$tags" tab="overview">
  <div x-data="{ photo: 0 }">
    @include('app.items.partials._overview')
  </div>
</x-item-page>
