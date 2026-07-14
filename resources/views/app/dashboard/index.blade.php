<x-app-layout>
  <x-slot:title>
    {{ __('Dashboard') }}
  </x-slot>

  @php
    $stats = [
      (object) ['label' => __('Members'), 'value' => $memberCount, 'delta' => trans_choice(':count person in this account|:count people in this account', $memberCount, ['count' => $memberCount]), 'deltaClass' => 'text-muted'],
      (object) ['label' => __('Pending invitations'), 'value' => $pendingInvitations, 'delta' => $pendingInvitations > 0 ? __('Awaiting acceptance') : __('None pending'), 'deltaClass' => 'text-muted'],
      (object) ['label' => __('Collections'), 'value' => '0', 'delta' => __('Coming soon'), 'deltaClass' => 'text-muted-soft'],
      (object) ['label' => __('Sets in progress'), 'value' => '0', 'delta' => __('Coming soon'), 'deltaClass' => 'text-muted-soft'],
    ];
  @endphp

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    {{-- Header --}}
    <div class="mb-9 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <h1 class="text-[28px] font-semibold tracking-tight text-ink">{{ $greeting }}, {{ $firstName }}</h1>
        <p class="mt-1 text-[15px] text-muted">{{ __("Here's what's happening across your account.") }}</p>
      </div>
      <x-button href="{{ route('collections.new') }}" turbo="true">
        <x-slot:icon>
          <x-lucide-plus class="size-4" />
        </x-slot>
        {{ __('New collection') }}
      </x-button>
    </div>

    {{-- Stat cards --}}
    <div class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      @foreach ($stats as $stat)
        <div class="flex flex-col gap-2 rounded-lg bg-card p-5">
          <span class="text-[13px] font-medium text-muted">{{ $stat->label }}</span>
          <span class="text-[28px] font-semibold tracking-tight text-ink">{{ $stat->value }}</span>
          <span class="text-[13px] font-medium {{ $stat->deltaClass }}">{{ $stat->delta }}</span>
        </div>
      @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1.6fr_1fr] lg:items-start">
      {{-- Collections (empty for now) --}}
      <div>
        <div class="mb-4 flex items-center justify-between">
          <h2 class="text-lg font-semibold text-ink">{{ __('Your collections') }}</h2>
        </div>
        <div class="rounded-lg border border-hairline">
          <x-empty-state>
            <x-slot:icon>
              <x-lucide-layers class="size-6 text-muted" />
            </x-slot>
            {{ __('No collections yet. Collections are coming soon.') }}
          </x-empty-state>
        </div>
      </div>

      {{-- Activity + set completion --}}
      <div class="space-y-7">
        <div>
          <h2 class="mb-4 text-lg font-semibold text-ink">{{ __('Recent activity') }}</h2>
          <div class="overflow-hidden rounded-lg border border-hairline">
            @forelse ($activity as $entry)
              <div class="flex gap-3 border-b border-hairline-soft px-4 py-3.5 last:border-b-0">
                <x-avatar-initials :name="$entry->name" class="size-8 text-xs" />
                <div class="min-w-0">
                  <p class="text-sm leading-snug text-body">
                    <span class="font-semibold text-ink">{{ $entry->name }}</span>
                    {{ $entry->description }}
                  </p>
                  <p class="mt-0.5 text-xs text-muted-soft">{{ $entry->createdAtHuman }}</p>
                </div>
              </div>
            @empty
              <x-empty-state>
                <x-slot:icon>
                  <x-lucide-activity class="size-6 text-muted" />
                </x-slot>
                {{ __('No activity yet.') }}
              </x-empty-state>
            @endforelse
          </div>
        </div>

        <div>
          <h2 class="mb-4 text-lg font-semibold text-ink">{{ __('Set completion') }}</h2>
          <div class="rounded-lg border border-hairline">
            <x-empty-state>
              <x-slot:icon>
                <x-lucide-layout-grid class="size-6 text-muted" />
              </x-slot>
              {{ __('Track set completion once collections arrive.') }}
            </x-empty-state>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
