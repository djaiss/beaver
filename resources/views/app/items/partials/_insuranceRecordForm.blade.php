{{--
  The form behind both adding and editing an insurance record.

  The insured value and the deductible are typed in currency units here and
  stored in cents, and both are in the one currency, which is why the selector
  sits with the insured value. The coverage fields come first and the broker
  contact is grouped after them. The ids are prefixed per form because the panel
  renders one of these per copy and one per record being edited, and duplicate
  ids would point every label at the first form.

  Expects: $formId, $action, $method, $openVar, $submitLabel, $dataTest,
  $record (null when adding), $currencies, $collection.
--}}

@use('App\Enums\InsuranceStatus')

@php
    $units = fn (?int $cents): string => $cents === null ? '' : number_format($cents / 100, 2, '.', '');
    $selectedCurrency = $record?->currency_code ?? $collection->currency ?? array_key_first($currencies);
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
      <div class="min-w-[200px] flex-1">
        <x-label for="{{ $formId }}-provider">{{ __('Provider') }}</x-label>
        <input id="{{ $formId }}-provider" name="provider" value="{{ $record?->provider }}" placeholder="{{ __('e.g. Collectibles Insurance Services') }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-provider" />
        <x-error :messages="$errors->get('provider')" class="mt-2" />
      </div>

      <div class="min-w-[150px]">
        <x-label for="{{ $formId }}-status">{{ __('Status') }}</x-label>
        <select id="{{ $formId }}-status" name="status" class="{{ $selectClasses }}" data-test="{{ $formId }}-status">
          @foreach (InsuranceStatus::options() as $value => $label)
            <option value="{{ $value }}" @selected($value === ($record?->status->value ?? InsuranceStatus::Active->value))>{{ $label }}</option>
          @endforeach
        </select>
        <x-error :messages="$errors->get('status')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-policy-number">{{ __('Policy number') }}</x-label>
        <input id="{{ $formId }}-policy-number" name="policy_number" value="{{ $record?->policy_number }}" placeholder="{{ __('e.g. CIS-88231') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('policy_number')" class="mt-2" />
      </div>

      <div class="min-w-[180px] flex-1">
        <x-label for="{{ $formId }}-coverage-type">{{ __('Coverage type') }}</x-label>
        <input id="{{ $formId }}-coverage-type" name="coverage_type" value="{{ $record?->coverage_type }}" placeholder="{{ __('e.g. Scheduled item, blanket contents') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('coverage_type')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[160px] flex-1">
        <x-label for="{{ $formId }}-insured-value">{{ __('Insured value') }}</x-label>
        <input id="{{ $formId }}-insured-value" name="insured_value" type="number" step="0.01" min="0" value="{{ $units($record?->insured_value) }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-insured-value" />
        <x-error :messages="$errors->get('insured_value')" class="mt-2" />
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

      <div class="min-w-[140px] flex-1">
        <x-label for="{{ $formId }}-deductible-amount">{{ __('Deductible') }}</x-label>
        <input id="{{ $formId }}-deductible-amount" name="deductible_amount" type="number" step="0.01" min="0" value="{{ $units($record?->deductible_amount) }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('deductible_amount')" class="mt-2" />
      </div>
    </div>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[160px] flex-1">
        <x-label for="{{ $formId }}-starts-at">{{ __('Starts') }}</x-label>
        <input id="{{ $formId }}-starts-at" name="starts_at" type="date" value="{{ $record?->starts_at?->toDateString() }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('starts_at')" class="mt-2" />
      </div>

      <div class="min-w-[160px] flex-1">
        <x-label for="{{ $formId }}-ends-at">{{ __('Ends') }}</x-label>
        <input id="{{ $formId }}-ends-at" name="ends_at" type="date" value="{{ $record?->ends_at?->toDateString() }}" class="{{ $inputClasses }}" />
        <p class="mt-1 text-xs text-muted">{{ __('Leave blank if the coverage is ongoing.') }}</p>
        <x-error :messages="$errors->get('ends_at')" class="mt-2" />
      </div>
    </div>

    <label for="{{ $formId }}-scheduled" class="mb-4 flex cursor-pointer items-center gap-3 rounded-md border border-hairline px-3.5 py-3">
      <input id="{{ $formId }}-scheduled" name="is_scheduled_item" type="checkbox" value="1" @checked($record?->is_scheduled_item) class="size-4 shrink-0 rounded border-hairline text-brand" data-test="{{ $formId }}-scheduled" />
      <span class="min-w-0">
        <span class="block text-[13px] font-semibold text-ink">{{ __('Individually scheduled item') }}</span>
        <span class="block text-xs text-muted-soft">{{ __('This copy is listed on the policy by name, not under blanket contents.') }}</span>
      </span>
    </label>

    <div class="mb-3.5 flex flex-wrap gap-3.5">
      <div class="min-w-[160px] flex-1">
        <x-label for="{{ $formId }}-contact-name">{{ __('Contact') }}</x-label>
        <input id="{{ $formId }}-contact-name" name="contact_name" value="{{ $record?->contact_name }}" placeholder="{{ __('Agent name') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('contact_name')" class="mt-2" />
      </div>

      <div class="min-w-[160px] flex-1">
        <x-label for="{{ $formId }}-contact-email">{{ __('Email') }}</x-label>
        <input id="{{ $formId }}-contact-email" name="contact_email" type="email" value="{{ $record?->contact_email }}" placeholder="{{ __('agent@insurer.com') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('contact_email')" class="mt-2" />
      </div>

      <div class="min-w-[160px] flex-1">
        <x-label for="{{ $formId }}-contact-phone">{{ __('Phone') }}</x-label>
        <input id="{{ $formId }}-contact-phone" name="contact_phone" value="{{ $record?->contact_phone }}" placeholder="{{ __('e.g. +1 888 837 9537') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('contact_phone')" class="mt-2" />
      </div>
    </div>

    <div class="mb-4">
      <x-label for="{{ $formId }}-note">{{ __('Note') }}</x-label>
      <textarea id="{{ $formId }}-note" name="note" rows="2" placeholder="{{ __('Anything worth recording about this coverage.') }}" class="mt-1.5 w-full rounded-md border border-hairline bg-input px-3 py-2 text-sm text-ink">{{ $record?->note }}</textarea>
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
