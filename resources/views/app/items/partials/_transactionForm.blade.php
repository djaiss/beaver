{{--
  The form behind both adding and editing a transaction.

  Money is typed in currency units here and stored in cents, and every amount on
  a transaction is in the one currency, which is why the selector sits on the
  same row as the figures. The total is computed from the four figures above it
  until someone types their own, at which point it is theirs until they reset it,
  so a recorded total wins over the parts only when it was meant to.

  The ids are prefixed per form because the history tab renders one add form and
  one edit form per transaction, and duplicate ids would point every label at the
  first form.

  Expects: $formId, $action, $method, $openVar, $submitLabel, $dataTest,
  $transaction (null when adding), $currencies, $collection. When editing, also
  $deleteAction.
--}}

@use('App\Enums\TransactionType')

@php
    $isEdit = $transaction !== null;
    $units = fn (?int $cents): string => $cents === null ? '' : number_format($cents / 100, 2, '.', '');
    $selectedCurrency = $transaction?->currency_code ?? $collection->currency ?? array_key_first($currencies);

    $labelClasses = 'block text-[11px] font-semibold tracking-wide text-muted-soft uppercase';
    $inputClasses = 'h-10 w-full rounded-md border border-hairline bg-input px-3 text-sm text-ink placeholder-muted-soft';
    $moneyClasses = 'h-10 w-full rounded-md border border-hairline bg-input pr-3 pl-6 text-sm text-ink';
    $selectClasses = 'h-10 w-full appearance-none rounded-md border border-hairline bg-input pr-9 pl-3 text-sm text-ink';
@endphp

@if ($isEdit)
  {{-- Deleting is its own form, hidden here and submitted by the Delete button in
       the footer through its form attribute, so it shares the footer row without
       nesting one form inside another. --}}
  <x-form
    method="delete"
    :action="$deleteAction"
    :id="$formId.'-delete'"
    x-target="history-panel notifications"
    class="hidden"
  ></x-form>
@endif

<x-form
  :method="$method"
  :action="$action"
  :id="$formId"
  :data-test="$dataTest"
  x-target="history-panel notifications"
  x-data="{
      amount: '{{ $units($transaction?->amount) }}',
      tax: '{{ $units($transaction?->tax_amount) }}',
      fees: '{{ $units($transaction?->fee_amount) }}',
      shipping: '{{ $units($transaction?->shipping_amount) }}',
      total: '{{ $units($transaction?->total_amount) }}',
      manual: {{ $transaction?->total_amount !== null ? 'true' : 'false' }},
      n(v) { return parseFloat(v) || 0 },
      get auto() { return (this.n(this.amount) + this.n(this.tax) + this.n(this.fees) + this.n(this.shipping)).toFixed(2) },
  }"
  x-effect="if (! manual) { total = (auto === '0.00' ? '' : auto) }"
  x-on:ajax:after="{{ $openVar }} = document.querySelector('#{{ $formId }}-fields .text-error') !== null"
  class="overflow-hidden rounded-xl border border-brand/60"
>
  <div class="flex items-center gap-2 border-b border-brand/15 bg-brand/8 px-5 py-3.5">
    <span class="size-2 shrink-0 rounded-full bg-brand"></span>
    <span class="text-sm font-semibold text-brand">{{ $isEdit ? __('Editing transaction') : __('New transaction') }}</span>
    @if ($transaction?->reference_number)
      <span class="truncate text-sm text-muted-soft">· {{ $transaction->reference_number }}</span>
    @endif
  </div>

  <div id="{{ $formId }}-fields" class="p-5">
    <div class="mb-3.5 grid grid-cols-1 gap-3.5 sm:grid-cols-2">
      <div>
        <label for="{{ $formId }}-type" class="{{ $labelClasses }} mb-1.5">{{ __('Type') }}</label>
        <div class="relative">
          <select id="{{ $formId }}-type" name="type" class="{{ $selectClasses }}" data-test="{{ $formId }}-type">
            @foreach (TransactionType::options() as $value => $label)
              <option value="{{ $value }}" @selected($value === ($transaction?->type->value ?? TransactionType::Purchase->value))>{{ $label }}</option>
            @endforeach
          </select>
          <x-lucide-chevrons-up-down class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted-soft" />
        </div>
        <x-error :messages="$errors->get('type')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-counterparty" class="{{ $labelClasses }} mb-1.5">{{ __('Counterparty') }}</label>
        <input id="{{ $formId }}-counterparty" name="counterparty" value="{{ $transaction?->counterparty }}" placeholder="{{ __('e.g. Heritage Auctions') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('counterparty')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 grid grid-cols-2 gap-3.5 sm:grid-cols-5">
      <div>
        <label for="{{ $formId }}-amount" class="{{ $labelClasses }} mb-1.5">{{ __('Amount') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-sm text-muted-soft">$</span>
          <input id="{{ $formId }}-amount" name="amount" type="number" step="0.01" min="0" x-model="amount" class="{{ $moneyClasses }}" data-test="{{ $formId }}-amount" />
        </div>
        <x-error :messages="$errors->get('amount')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-tax-amount" class="{{ $labelClasses }} mb-1.5">{{ __('Tax') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-sm text-muted-soft">$</span>
          <input id="{{ $formId }}-tax-amount" name="tax_amount" type="number" step="0.01" min="0" x-model="tax" class="{{ $moneyClasses }}" />
        </div>
        <x-error :messages="$errors->get('tax_amount')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-fee-amount" class="{{ $labelClasses }} mb-1.5">{{ __('Fees') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-sm text-muted-soft">$</span>
          <input id="{{ $formId }}-fee-amount" name="fee_amount" type="number" step="0.01" min="0" x-model="fees" class="{{ $moneyClasses }}" />
        </div>
        <x-error :messages="$errors->get('fee_amount')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-shipping-amount" class="{{ $labelClasses }} mb-1.5">{{ __('Shipping') }}</label>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-sm text-muted-soft">$</span>
          <input id="{{ $formId }}-shipping-amount" name="shipping_amount" type="number" step="0.01" min="0" x-model="shipping" class="{{ $moneyClasses }}" />
        </div>
        <x-error :messages="$errors->get('shipping_amount')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-currency" class="{{ $labelClasses }} mb-1.5">{{ __('Currency') }}</label>
        <div class="relative">
          <select id="{{ $formId }}-currency" name="currency" class="{{ $selectClasses }}">
            @foreach ($currencies as $code => $label)
              <option value="{{ $code }}" @selected($code === $selectedCurrency)>{{ $label }}</option>
            @endforeach
          </select>
          <x-lucide-chevrons-up-down class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted-soft" />
        </div>
        <x-error :messages="$errors->get('currency')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 grid grid-cols-1 gap-3.5 sm:grid-cols-2">
      <div>
        <div class="mb-1.5 flex items-center gap-2">
          <label for="{{ $formId }}-total-amount" class="{{ $labelClasses }}">{{ __('Total') }}</label>
          <button type="button" x-show="manual" x-cloak x-on:click="manual = false" class="text-[11px] font-semibold text-brand">· {{ __('manual (reset to auto)') }}</button>
        </div>
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center text-sm text-muted-soft">$</span>
          <input id="{{ $formId }}-total-amount" type="number" step="0.01" min="0" x-model="total" x-on:input="manual = true" placeholder="{{ __('Auto') }}" class="{{ $moneyClasses }}" />
        </div>
        <input type="hidden" name="total_amount" :value="manual ? total : ''" />
        <x-error :messages="$errors->get('total_amount')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-occurred-at" class="{{ $labelClasses }} mb-1.5">{{ __('Date') }}</label>
        <input id="{{ $formId }}-occurred-at" name="occurred_at" type="date" value="{{ $transaction?->occurred_at->toDateString() }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-occurred-at" />
        <x-error :messages="$errors->get('occurred_at')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 grid grid-cols-1 gap-3.5 sm:grid-cols-2">
      <div>
        <label for="{{ $formId }}-reference-number" class="{{ $labelClasses }} mb-1.5">{{ __('Reference') }}</label>
        <input id="{{ $formId }}-reference-number" name="reference_number" value="{{ $transaction?->reference_number }}" placeholder="{{ __('e.g. Lot #4021') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('reference_number')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-source-url" class="{{ $labelClasses }} mb-1.5">{{ __('Source URL') }}</label>
        <input id="{{ $formId }}-source-url" name="source_url" type="url" value="{{ $transaction?->source_url }}" placeholder="{{ __('Where this transaction can be checked') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('source_url')" class="mt-2" />
      </div>
    </div>

    <div class="mb-4">
      <label for="{{ $formId }}-note" class="{{ $labelClasses }} mb-1.5">{{ __('Note') }}</label>
      <textarea id="{{ $formId }}-note" name="note" rows="2" placeholder="{{ __('Anything worth remembering about this transaction.') }}" class="w-full rounded-md border border-hairline bg-input px-3 py-2 text-sm text-ink placeholder-muted-soft">{{ $transaction?->note }}</textarea>
      <x-error :messages="$errors->get('note')" class="mt-2" />
    </div>

    <div class="flex items-center justify-between gap-2.5">
      <div class="flex items-center gap-2.5">
        <x-button type="submit" class="text-[13px]" data-test="{{ $formId }}-submit">
          {{ $submitLabel }}
        </x-button>

        <x-button.secondary type="button" x-on:click="{{ $openVar }} = false" class="text-[13px]">
          {{ __('Cancel') }}
        </x-button.secondary>
      </div>

      @if ($isEdit)
        <button type="submit" form="{{ $formId }}-delete" x-on:click="if (! confirm('{{ __('Delete this transaction? This cannot be undone.') }}')) { $event.preventDefault() }" class="text-[13px] font-semibold text-error hover:underline" data-test="delete-transaction-{{ $transaction->id }}">
          {{ __('Delete') }}
        </button>
      @endif
    </div>
  </div>
</x-form>
