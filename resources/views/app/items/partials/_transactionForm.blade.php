{{--
  The form behind both adding and editing a transaction.

  Money is typed in currency units here and stored in cents, and every amount on
  a transaction is in the one currency, which is why the selector sits with the
  totals rather than next to each field. The ids are prefixed per form because
  the history tab renders one of these per copy and one per transaction being
  edited, and duplicate ids would point every label at the first form.

  Expects: $formId, $action, $method, $openVar, $submitLabel, $dataTest,
  $transaction (null when adding), $currencies, $collection.
--}}

@use('App\Enums\TransactionType')

@php
    $units = fn (?int $cents): string => $cents === null ? '' : number_format($cents / 100, 2, '.', '');
    $selectedCurrency = $transaction?->currency_code ?? $collection->currency ?? array_key_first($currencies);
    $inputClasses = 'mt-1.5 h-9 w-full rounded-md border border-hairline bg-input px-3 text-sm text-ink';
    $selectClasses = 'mt-1.5 h-9 w-full appearance-none rounded-md border border-hairline bg-input pr-9 pl-3 text-sm text-ink';
@endphp

<x-form
  :method="$method"
  :action="$action"
  :data-test="$dataTest"
  x-target="history-panel notifications"
  x-on:ajax:after="{{ $openVar }} = document.querySelector('#{{ $formId }}-fields .text-error') !== null"
>
  <div id="{{ $formId }}-fields">
    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-type">{{ __('Type') }}</x-label>
        <select id="{{ $formId }}-type" name="type" class="{{ $selectClasses }}" data-test="{{ $formId }}-type">
          @foreach (TransactionType::options() as $value => $label)
            <option value="{{ $value }}" @selected($value === ($transaction?->type->value ?? TransactionType::Purchase->value))>{{ $label }}</option>
          @endforeach
        </select>
        <x-error :messages="$errors->get('type')" class="mt-2" />
      </div>

      <div class="min-w-[160px]">
        <x-label for="{{ $formId }}-occurred-at">{{ __('Date') }}</x-label>
        <input id="{{ $formId }}-occurred-at" name="occurred_at" type="date" value="{{ $transaction?->occurred_at->toDateString() }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-occurred-at" />
        <x-error :messages="$errors->get('occurred_at')" class="mt-2" />
      </div>

      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-counterparty">{{ __('Counterparty') }}</x-label>
        <input id="{{ $formId }}-counterparty" name="counterparty" value="{{ $transaction?->counterparty }}" placeholder="{{ __('e.g. Central Perk Comics') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('counterparty')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 grid grid-cols-2 gap-3.5 sm:grid-cols-4">
      <div>
        <x-label for="{{ $formId }}-amount">{{ __('Amount') }}</x-label>
        <input id="{{ $formId }}-amount" name="amount" type="number" step="0.01" min="0" value="{{ $units($transaction?->amount) }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-amount" />
        <x-error :messages="$errors->get('amount')" class="mt-2" />
      </div>

      <div>
        <x-label for="{{ $formId }}-tax-amount">{{ __('Tax') }}</x-label>
        <input id="{{ $formId }}-tax-amount" name="tax_amount" type="number" step="0.01" min="0" value="{{ $units($transaction?->tax_amount) }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('tax_amount')" class="mt-2" />
      </div>

      <div>
        <x-label for="{{ $formId }}-fee-amount">{{ __('Fees') }}</x-label>
        <input id="{{ $formId }}-fee-amount" name="fee_amount" type="number" step="0.01" min="0" value="{{ $units($transaction?->fee_amount) }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('fee_amount')" class="mt-2" />
      </div>

      <div>
        <x-label for="{{ $formId }}-shipping-amount">{{ __('Shipping') }}</x-label>
        <input id="{{ $formId }}-shipping-amount" name="shipping_amount" type="number" step="0.01" min="0" value="{{ $units($transaction?->shipping_amount) }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('shipping_amount')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[160px] flex-1">
        <x-label for="{{ $formId }}-total-amount">{{ __('Total') }}</x-label>
        <input id="{{ $formId }}-total-amount" name="total_amount" type="number" step="0.01" min="0" value="{{ $units($transaction?->total_amount) }}" class="{{ $inputClasses }}" />
        <p class="mt-1 text-xs text-muted">{{ __('Leave empty to add the four figures above together.') }}</p>
        <x-error :messages="$errors->get('total_amount')" class="mt-2" />
      </div>

      <div class="min-w-[140px]">
        <x-label for="{{ $formId }}-currency">{{ __('Currency') }}</x-label>
        <select id="{{ $formId }}-currency" name="currency" class="{{ $selectClasses }}">
          @foreach ($currencies as $code => $label)
            <option value="{{ $code }}" @selected($code === $selectedCurrency)>{{ $label }}</option>
          @endforeach
        </select>
        <x-error :messages="$errors->get('currency')" class="mt-2" />
      </div>

      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-reference-number">{{ __('Reference number') }}</x-label>
        <input id="{{ $formId }}-reference-number" name="reference_number" value="{{ $transaction?->reference_number }}" placeholder="{{ __('e.g. Invoice 4021') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('reference_number')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5">
      <x-label for="{{ $formId }}-source-url">{{ __('Source link') }}</x-label>
      <input id="{{ $formId }}-source-url" name="source_url" type="url" value="{{ $transaction?->source_url }}" placeholder="{{ __('Where this transaction can be checked') }}" class="{{ $inputClasses }}" />
      <x-error :messages="$errors->get('source_url')" class="mt-2" />
    </div>

    <div class="mb-4">
      <x-label for="{{ $formId }}-note">{{ __('Note') }}</x-label>
      <textarea id="{{ $formId }}-note" name="note" rows="2" placeholder="{{ __('Anything worth remembering about this transaction.') }}" class="mt-1.5 w-full rounded-md border border-hairline bg-input px-3 py-2 text-sm text-ink">{{ $transaction?->note }}</textarea>
      <x-error :messages="$errors->get('note')" class="mt-2" />
    </div>

    <div class="flex justify-end gap-2.5">
      <x-button.secondary type="button" x-on:click="{{ $openVar }} = false" class="text-[13px]">
        {{ __('Cancel') }}
      </x-button.secondary>

      <x-button type="submit" class="text-[13px]" data-test="{{ $formId }}-submit">
        {{ $submitLabel }}
      </x-button>
    </div>
  </div>
</x-form>
