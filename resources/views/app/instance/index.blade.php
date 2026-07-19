<x-app-layout>
  <x-slot:title>
    {{ __('Instance overview') }}
  </x-slot>

  @php
    $peak = collect($signups)->max('count') ?: 1;

    $tiles = [
      ['label' => __('Accounts'), 'value' => $accountCount, 'hint' => __(':count created this month', ['count' => $accountsThisMonth])],
      ['label' => __('Users'), 'value' => $userCount, 'hint' => __(':count active this month', ['count' => $activeThisMonth])],
      ['label' => __('Collections'), 'value' => $collectionCount, 'hint' => null],
      ['label' => __('Items tracked'), 'value' => $itemCount, 'hint' => null],
    ];
  @endphp

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-5xl space-y-8">
      <div>
        <h1 class="text-[22px] font-semibold tracking-tight text-ink">{{ __('Instance overview') }}</h1>
        <p class="mt-1 text-sm text-muted">{{ __('Activity across every account on this Beaver instance.') }}</p>
      </div>

      {{-- Counts --}}
      <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ($tiles as $tile)
          <div class="rounded-lg border border-hairline bg-canvas p-4">
            <p class="text-xs font-medium tracking-wide text-muted-soft uppercase">{{ $tile['label'] }}</p>
            <p class="mt-2 text-2xl font-semibold text-ink">{{ number_format($tile['value']) }}</p>
            @if ($tile['hint'])
              <p class="mt-1 text-xs text-muted">{{ $tile['hint'] }}</p>
            @endif
          </div>
        @endforeach
      </div>

      {{-- Signups --}}
      <x-box title="{{ __('New accounts') }}">
        <x-slot:description>
          {{ __('Accounts created per month over the last year.') }}
        </x-slot>

        <div class="flex h-40 items-end gap-1.5">
          @foreach ($signups as $month)
            <div class="flex flex-1 flex-col items-center gap-1.5">
              <span class="text-xs text-muted">{{ $month['count'] ?: '' }}</span>
              <div
                class="w-full rounded-t bg-accent/70"
                style="height: {{ max(2, (int) round(($month['count'] / $peak) * 100)) }}%"
                title="{{ $month['count'] }}"
              ></div>
              <span class="text-[10px] text-muted-soft">{{ $month['label'] }}</span>
            </div>
          @endforeach
        </div>
      </x-box>

      {{-- Everything the mockup showed that Beaver cannot answer yet. Better to
           name them than to invent numbers. --}}
      <x-box title="{{ __('Not tracked yet') }}">
        <x-slot:description>
          {{ __('Beaver does not have the data behind these yet.') }}
        </x-slot>

        <ul class="space-y-2.5">
          @foreach ([__('Billing plans'), __('Storage and API quotas'), __('Support tickets'), __('User reviews')] as $item)
            <li class="flex items-center justify-between text-sm text-muted">
              {{ $item }}
              <x-soon />
            </li>
          @endforeach
        </ul>
      </x-box>
    </div>
  </div>
</x-app-layout>
