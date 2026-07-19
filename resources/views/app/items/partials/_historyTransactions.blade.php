{{--
  The transactions of the copy, newest first. Transactions are the single source
  of truth for the commercial data: the price paid, the tax, the fees and the
  shipping, and the total that actually changed hands.

  An add form opens above the list, and an edit form takes the place of the card
  it belongs to, so a transaction is never shown and edited at the same time.
--}}

@use('App\Helpers\Money')

@php
  $user = auth()->user();
  $canManage = $user->account->allowsManagementBy($user);
@endphp

<div x-data="{ adding: false }">
  <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
    <div class="min-w-0">
      <p class="text-lg font-semibold text-ink">{{ __('Transactions') }}</p>
      <p class="mt-1 max-w-xl text-[13px] leading-relaxed text-muted">{{ __('Financial and ownership exchanges. The source of truth for prices, fees and totals.') }}</p>
    </div>

    @if ($canManage)
      <x-button.secondary type="button" x-on:click="adding = ! adding" class="shrink-0 text-[13px]" data-test="new-transaction-{{ $selectedCopy->id }}">
        <x-slot:icon>
          <x-lucide-plus class="size-4" />
        </x-slot>
        {{ __('Add transaction') }}
      </x-button.secondary>
    @endif
  </div>

  @if ($canManage)
    <div x-show="adding" x-cloak class="mb-4">
      @include('app.items.partials._transactionForm', [
          'formId' => 'add-transaction-'.$selectedCopy->id,
          'action' => route('transactions.create', [$collection, $item, $selectedCopy]),
          'method' => 'post',
          'openVar' => 'adding',
          'submitLabel' => __('Add transaction'),
          'dataTest' => 'create-transaction-form-'.$selectedCopy->id,
          'transaction' => null,
      ])
    </div>
  @endif

  <div class="flex flex-col gap-3">
    @forelse ($selectedCopy->transactions as $transaction)
      @php
        $total = $transaction->total();
        $breakdown = [
            __('Amount') => $transaction->amount,
            __('Tax') => $transaction->tax_amount,
            __('Fees') => $transaction->fee_amount,
            __('Shipping') => $transaction->shipping_amount,
        ];
      @endphp

      <div x-data="{ editing: false }" data-test="transaction-{{ $transaction->id }}">
        <div x-show="! editing" class="overflow-hidden rounded-xl border border-hairline">
          <div class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
            <div class="flex min-w-0 items-center gap-3">
              <span class="inline-flex shrink-0 items-center rounded-full bg-success/10 px-2.5 py-1 text-[11px] font-semibold tracking-wide text-success uppercase" data-test="transaction-type-{{ $transaction->id }}">{{ $transaction->type->label() }}</span>

              @if ($transaction->counterparty)
                <p class="truncate text-[15px] font-semibold text-ink" data-test="transaction-counterparty-{{ $transaction->id }}">{{ $transaction->counterparty }}</p>
              @else
                <p class="truncate text-[15px] text-muted-soft" data-test="transaction-counterparty-{{ $transaction->id }}">{{ __('No counterparty recorded.') }}</p>
              @endif
            </div>

            <div class="flex items-start gap-3">
              <div class="text-right">
                <p class="text-lg font-semibold text-ink" data-test="transaction-total-{{ $transaction->id }}">{{ $total === null ? '—' : Money::format($total, $transaction->currency_code) }}</p>
                <p class="text-xs text-muted-soft">{{ $transaction->occurred_at->isoFormat('MMM D, YYYY') }}</p>
              </div>

              @if ($canManage)
                <x-button.secondary type="button" x-on:click="editing = true" class="h-9 shrink-0 text-[13px]" data-test="edit-transaction-{{ $transaction->id }}">
                  {{ __('Edit') }}
                </x-button.secondary>
              @endif
            </div>
          </div>

          <div class="grid grid-cols-2 border-t border-hairline sm:grid-cols-4">
            @foreach ($breakdown as $label => $cents)
              <div class="border-b border-hairline px-5 py-3.5 last:border-r-0 sm:border-b-0 sm:border-r sm:border-r-hairline">
                <p class="mb-1 text-xs text-muted-soft">{{ $label }}</p>
                <p class="truncate text-sm font-semibold text-ink">{{ $cents === null ? '—' : Money::format($cents, $transaction->currency_code) }}</p>
              </div>
            @endforeach
          </div>

          @if ($transaction->reference_number || $transaction->note)
            <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1 border-t border-hairline px-5 py-3.5">
              @if ($transaction->reference_number)
                <span class="shrink-0 text-[13px] text-muted-soft">{{ __('Ref') }} <span class="font-mono text-ink" data-test="transaction-reference-{{ $transaction->id }}">{{ $transaction->reference_number }}</span></span>
              @endif

              @if ($transaction->note)
                <span class="min-w-0 flex-1 text-[13px] leading-relaxed text-muted">{{ $transaction->note }}</span>
              @endif
            </div>
          @endif
        </div>

        @if ($canManage)
          <div x-show="editing" x-cloak>
            @include('app.items.partials._transactionForm', [
                'formId' => 'edit-transaction-'.$transaction->id,
                'action' => route('transactions.update', [$collection, $item, $selectedCopy, $transaction]),
                'deleteAction' => route('transactions.destroy', [$collection, $item, $selectedCopy, $transaction]),
                'method' => 'put',
                'openVar' => 'editing',
                'submitLabel' => __('Save changes'),
                'dataTest' => 'edit-transaction-form-'.$transaction->id,
                'transaction' => $transaction,
            ])
          </div>
        @endif
      </div>
    @empty
      <div class="rounded-xl border border-hairline">
        <x-empty-state data-test="no-transactions-{{ $selectedCopy->id }}">
          <x-slot:icon>
            <x-lucide-receipt class="size-6 text-muted" />
          </x-slot>

          {{ __('No transaction has been recorded against this copy yet.') }}
        </x-empty-state>
      </div>
    @endforelse
  </div>
</div>
