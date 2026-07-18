@use('App\Enums\FieldTypeEnum')

@php
    $value = $display($field);
@endphp

<div class="border-b border-hairline px-4.5 py-3.5 last:border-b-0 sm:odd:border-r sm:odd:border-r-hairline">
  <p class="mb-1 text-xs text-muted-soft">{{ $field->name }}</p>

  @if ($field->field_type === FieldTypeEnum::Rating && $value !== null)
    <p class="text-sm font-semibold text-ink" aria-label="{{ trans_choice(':count star|:count stars', (int) $value, ['count' => $value]) }}">
      @for ($star = 1; $star <= FieldTypeEnum::MAX_RATING; $star++)
        <span class="{{ $star <= (int) $value ? 'text-ink' : 'text-hairline' }}">★</span>
      @endfor
    </p>
  @else
    <p class="text-sm font-semibold text-ink">{{ $value ?? '—' }}</p>
  @endif
</div>
