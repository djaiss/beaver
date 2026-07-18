@props(['collection', 'item', 'tags', 'tab'])

@php
    $user = auth()->user();
    $canManage = $user->account->allowsManagementBy($user);
    $type = $item->collectionType;

    // Every tab is its own page, so the active one is decided here rather than
    // in the browser. Overview is the item's own url, the rest hang off it.
    $navigation = [
        ['overview', __('Overview'), null, route('items.show', [$collection, $item])],
        ['copies', __('Copies'), $item->copies->count(), route('items.copies.index', [$collection, $item])],
        ['activities', __('Activity'), null, route('items.activities.index', [$collection, $item])],
        ['roadmap', __('Roadmap'), null, route('items.roadmap.index', [$collection, $item])],
    ];
@endphp

<x-app-layout :collection="$collection">
  <x-slot:title>
    {{ $item->name }}
  </x-slot>

  <div class="px-6 py-8 lg:px-10">
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

        {{-- Adding a copy on its own has no screen on the web yet. --}}
        <div class="flex shrink-0 flex-wrap items-center gap-2">
          @if ($canManage)
            <a
              href="{{ route('items.edit', [$collection, $item]) }}"
              data-turbo="true"
              class="flex h-9 items-center gap-2 rounded-md border border-hairline px-3.5 text-[13px] font-semibold text-ink transition-colors hover:bg-card"
              data-test="edit-item-button"
            >
              @svg('lucide-pencil', 'size-3.5 text-muted')
              {{ __('Edit') }}
            </a>
          @endif
          <span class="flex h-9 cursor-not-allowed items-center gap-2 rounded-md border border-hairline px-3.5 text-[13px] font-semibold text-muted-soft" data-test="add-copy-soon">
            {{ __('Add copy') }}
            <x-soon />
          </span>
        </div>
      </div>

      {{-- Tags --}}
      @include('app.items.partials._tags')

      {{-- Tabs --}}
      {{-- The tabs scroll sideways on a narrow screen. Pinning overflow-y stops the
           browser turning that into a vertical scrollbar, which it otherwise does to
           reach the 1px the active tab's underline hangs over the border by. --}}
      <div class="mb-7 flex items-center gap-1 overflow-x-auto overflow-y-hidden border-b border-hairline">
        @foreach ($navigation as [$key, $label, $count, $url])
          <a
            href="{{ $url }}"
            data-turbo="true"
            @class([
                '-mb-px flex items-center gap-2 border-b-2 px-3.5 py-3 text-sm whitespace-nowrap transition-colors',
                'border-ink font-semibold text-ink' => $tab === $key,
                'border-transparent font-medium text-muted hover:text-ink' => $tab !== $key,
            ])
            @if ($tab === $key) aria-current="page" @endif
            data-test="item-tab-{{ $key }}"
          >
            {{ $label }}
            @if ($count !== null)
              <span class="rounded-full bg-card px-1.5 py-px text-[11px] font-semibold text-muted">{{ $count }}</span>
            @endif
          </a>
        @endforeach
      </div>

      {{ $slot }}
    </div>
  </div>
</x-app-layout>
