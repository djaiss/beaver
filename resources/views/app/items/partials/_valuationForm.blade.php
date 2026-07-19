{{--
  The form behind both adding and editing a valuation.

  The amount is typed in currency units here and stored in cents. Editing is the
  exception rather than the rule: valuations are historical records, so the
  normal way to change what a copy is worth is to add a new one. The ids are
  prefixed per form because the panel renders one of these per copy and one per
  valuation being edited, and duplicate ids would point every label at the first
  form.

  Expects: $formId, $action, $method, $openVar, $submitLabel, $dataTest,
  $valuation (null when adding), $currencies, $collection.
--}}

@use('App\Enums\ValuationConfidence')
@use('App\Enums\ValuationType')

@php
    $units = fn (?int $cents): string => $cents === null ? '' : number_format($cents / 100, 2, '.', '');
    $selectedCurrency = $valuation?->currency_code ?? $collection->currency ?? array_key_first($currencies);
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
          @foreach (ValuationType::options() as $value => $label)
            <option value="{{ $value }}" @selected($value === ($valuation?->type->value ?? ValuationType::UserEstimate->value))>{{ $label }}</option>
          @endforeach
        </select>
        <x-error :messages="$errors->get('type')" class="mt-2" />
      </div>

      <div class="min-w-[160px]">
        <x-label for="{{ $formId }}-valued-at">{{ __('Date') }}</x-label>
        <input id="{{ $formId }}-valued-at" name="valued_at" type="date" value="{{ $valuation?->valued_at->toDateString() }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-valued-at" />
        <x-error :messages="$errors->get('valued_at')" class="mt-2" />
      </div>

      <div class="min-w-[140px]">
        <x-label for="{{ $formId }}-confidence">{{ __('Confidence') }}</x-label>
        <select id="{{ $formId }}-confidence" name="confidence" class="{{ $selectClasses }}">
          @foreach (ValuationConfidence::options() as $value => $label)
            <option value="{{ $value }}" @selected($value === ($valuation?->confidence->value ?? ValuationConfidence::Unknown->value))>{{ $label }}</option>
          @endforeach
        </select>
        <x-error :messages="$errors->get('confidence')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[140px] flex-1">
        <x-label for="{{ $formId }}-amount">{{ __('Amount') }}</x-label>
        <input id="{{ $formId }}-amount" name="amount" type="number" step="0.01" min="0" value="{{ $units($valuation?->amount) }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-amount" />
        <x-error :messages="$errors->get('amount')" class="mt-2" />
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
    </div>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-valuer">{{ __('Valuer') }}</x-label>
        <input id="{{ $formId }}-valuer" name="valuer" value="{{ $valuation?->valuer }}" placeholder="{{ __('e.g. Central Perk Appraisals') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('valuer')" class="mt-2" />
      </div>

      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-method">{{ __('Method') }}</x-label>
        <input id="{{ $formId }}-method" name="method" value="{{ $valuation?->method }}" placeholder="{{ __('e.g. Comparable sales') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('method')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-reference-number">{{ __('Reference number') }}</x-label>
        <input id="{{ $formId }}-reference-number" name="reference_number" value="{{ $valuation?->reference_number }}" placeholder="{{ __('e.g. CP-1994') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('reference_number')" class="mt-2" />
      </div>

      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-source-url">{{ __('Source link') }}</x-label>
        <input id="{{ $formId }}-source-url" name="source_url" type="url" value="{{ $valuation?->source_url }}" placeholder="{{ __('Where this valuation can be checked') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('source_url')" class="mt-2" />
      </div>
    </div>

    <div class="mb-4">
      <x-label for="{{ $formId }}-note">{{ __('Note') }}</x-label>
      <textarea id="{{ $formId }}-note" name="note" rows="2" placeholder="{{ __('Anything worth remembering about this valuation.') }}" class="mt-1.5 w-full rounded-md border border-hairline bg-input px-3 py-2 text-sm text-ink">{{ $valuation?->note }}</textarea>
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
