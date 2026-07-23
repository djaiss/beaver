{{-- The Deposits tab: what is held or owed across open loans, and the loans that carry one. --}}
@use('App\Enums\LoanDirection')
@use('App\Helpers\Money')

@php
  $isOut = $direction === LoanDirection::Outgoing;
  $totals = $tabData['totals'];
  $totalText = $totals === [] ? Money::format(0, null) : collect($totals)->map(fn ($cents, $code) => Money::format($cents, $code))->join(' + ');
  $sideWord = fn ($loan) => $loan->status === \App\Enums\LoanStatus::Returned ? __('returned') : ($isOut ? __('held') : __('owed'));
@endphp

<div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
  <div class="rounded-xl border border-hairline bg-canvas p-4">
    <div class="text-[12px] font-medium text-muted">{{ $isOut ? __('Deposits held') : __('Deposits owed') }}</div>
    <div class="mt-1.5 text-2xl font-semibold text-ink">{{ $totalText }}</div>
    <div class="text-[11px] text-muted-soft">{{ __('across open loans') }}</div>
  </div>
  <div class="rounded-xl border border-hairline bg-canvas p-4">
    <div class="text-[12px] font-medium text-muted">{{ __('Loans with a deposit') }}</div>
    <div class="mt-1.5 text-2xl font-semibold text-ink">{{ number_format($tabData['count']) }}</div>
    <div class="text-[11px] text-muted-soft">{{ __('in this direction') }}</div>
  </div>
  <div class="rounded-xl border border-hairline bg-canvas p-4">
    <div class="text-[12px] font-medium text-muted">{{ __('Default currency') }}</div>
    <div class="mt-1.5 text-2xl font-semibold text-ink">{{ __('Per collection') }}</div>
    <div class="text-[11px] text-muted-soft">{{ __('the deposit currency defaults to the collection\'s') }}</div>
  </div>
</div>

<div class="overflow-hidden rounded-xl border border-hairline bg-canvas">
  @forelse ($tabData['loans'] as $loan)
    <a href="{{ route('loans.detail', ['direction' => $direction->slug(), 'tab' => $tab, 'loan' => $loan->id]) }}" data-turbo="true" class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-canvas">
      <div class="min-w-0 flex-1">
        <div class="truncate text-sm font-medium text-ink">{{ $loan->copy->item->name }}</div>
        <div class="truncate text-[12px] text-muted">{{ $loan->party }}</div>
      </div>
      <div class="w-28 shrink-0 text-sm font-medium text-ink">{{ Money::format($loan->deposit_amount, $loan->deposit_currency_code) }}</div>
      <div class="hidden w-20 shrink-0 text-[13px] text-muted sm:block">{{ $sideWord($loan) }}</div>
      <div class="shrink-0">@include('app.loans.partials._statusBadge', ['status' => $loan->status])</div>
    </a>
    @if (! $loop->last)
      <div class="border-b border-hairline"></div>
    @endif
  @empty
    <x-empty-state data-test="no-deposits">
      <x-slot:icon>
        <x-lucide-banknote class="size-6 text-muted" />
      </x-slot>
      {{ __('No deposits recorded for these loans.') }}
    </x-empty-state>
  @endforelse
</div>
