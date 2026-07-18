@use('App\Enums\FieldTypeEnum')
@use('App\Helpers\Money')

@php
    $type = $item->collectionType;
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

    $money = fn (int $cents): string => Money::format($cents, $collection->currency);

    $totalEstimated = (int) $item->copies->sum('estimated_value');
    $totalPaid = (int) $item->copies->sum('price_paid');

    $ungroupedFields = $type?->ungroupedCustomFields ?? collect();
    $fieldGroups = $type?->customFieldGroups ?? collect();
    $hasDetails = $ungroupedFields->isNotEmpty() || $fieldGroups->isNotEmpty();
@endphp

<x-app-layout :collection="$collection">
  <x-slot:title>
    {{ $item->name }}
  </x-slot>

  <div class="px-6 py-8 lg:px-10" x-data="{ tab: 'overview', photo: 0 }">
    <div class="mx-auto w-full max-w-5xl">
      {{-- Breadcrumb --}}
      <div class="mb-5 flex flex-wrap items-center gap-1.5 text-[13px]">
        <a href="{{ route('collections.index') }}" data-turbo="true" class="font-medium text-muted-soft transition-colors hover:text-ink">{{ __('Collections') }}</a>
        <span class="text-muted-soft">/</span>
        <a href="{{ route('collections.show', $collection) }}" data-turbo="true" class="font-medium text-muted-soft transition-colors hover:text-ink">{{ $collection->name }}</a>
        @if ($item->category)
          <span class="text-muted-soft">/</span>
          <span class="font-medium text-muted-soft">{{ $item->category->name }}</span>
        @endif
        <span class="text-muted-soft">/</span>
        <span class="truncate font-medium text-ink">{{ $item->name }}</span>
      </div>

      {{-- Header --}}
      <div class="mb-3.5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
          <div class="mb-2 flex flex-wrap items-center gap-2.5">
            @if ($type)
              <span class="inline-flex items-center gap-2 rounded-full bg-card px-3 py-1 text-xs font-semibold text-ink">
                <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $type->color }}"></span>
                {{ $type->name }}
              </span>
            @endif

            @if ($item->category)
              <span class="text-xs font-medium text-muted-soft">{{ __('in :name', ['name' => $item->category->name]) }}</span>
            @endif
          </div>

          <h1 class="text-[28px] leading-tight font-semibold tracking-tight text-ink" data-test="item-name">{{ $item->name }}</h1>
        </div>

        {{-- Editing an item and adding a copy have no screens on the web yet. --}}
        <div class="flex shrink-0 flex-wrap items-center gap-2">
          <span class="flex h-9 cursor-not-allowed items-center gap-2 rounded-md border border-hairline px-3.5 text-[13px] font-semibold text-muted-soft" data-test="edit-item-soon">
            {{ __('Edit') }}
            <x-soon />
          </span>
          <span class="flex h-9 cursor-not-allowed items-center gap-2 rounded-md border border-hairline px-3.5 text-[13px] font-semibold text-muted-soft" data-test="add-copy-soon">
            {{ __('Add copy') }}
            <x-soon />
          </span>
        </div>
      </div>

      {{-- Tags --}}
      <div class="mb-6 flex flex-wrap items-center gap-2">
        @foreach ($item->tags as $tag)
          <x-badge>{{ $tag->name }}</x-badge>
        @endforeach

        <span class="inline-flex cursor-not-allowed items-center gap-2 rounded-full border border-dashed border-hairline px-3 py-1 text-[13px] font-medium text-muted-soft">
          {{ __('Add tag') }}
          <x-soon />
        </span>
      </div>

      {{-- Tabs --}}
      <div class="mb-7 flex items-center gap-1 overflow-x-auto border-b border-hairline">
        @foreach ([['overview', __('Overview'), null], ['copies', __('Copies'), $item->copies->count()], ['roadmap', __('Roadmap'), null]] as [$key, $label, $count])
          <button
            type="button"
            x-on:click="tab = @js($key)"
            :class="tab === @js($key) ? 'border-ink font-semibold text-ink' : 'border-transparent font-medium text-muted hover:text-ink'"
            class="-mb-px flex cursor-pointer items-center gap-2 border-b-2 px-3.5 py-3 text-sm whitespace-nowrap"
            data-test="item-tab-{{ $key }}"
          >
            {{ $label }}
            @if ($count !== null)
              <span class="rounded-full bg-card px-1.5 py-px text-[11px] font-semibold text-muted">{{ $count }}</span>
            @endif
          </button>
        @endforeach
      </div>

      <div x-show="tab === 'overview'">
        @include('app.items.partials._overview')
      </div>

      <div x-show="tab === 'copies'" x-cloak>
        @include('app.items.partials._copies')
      </div>

      <div x-show="tab === 'roadmap'" x-cloak>
        @include('app.items.partials._roadmap')
      </div>
    </div>
  </div>
</x-app-layout>
