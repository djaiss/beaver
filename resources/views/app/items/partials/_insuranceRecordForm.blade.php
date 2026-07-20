{{--
  The card behind both adding and editing an insurance record.

  The whole thing is one framed card: a header naming what is being done, the
  coverage fields, the broker contact grouped after them, and a footer with the
  actions. The insured value and the deductible are typed in currency units here
  and stored in cents, and both are in the one currency, which is why the selector
  is joined to the insured value. The ids are prefixed per form because the panel
  renders one of these per copy and one per record being edited, and duplicate
  ids would point every label at the first form.

  Expects: $formId, $action, $method, $openVar, $submitLabel, $dataTest,
  $formTitle, $hint (null on edit), $record (null when adding), $currencies,
  $collection.
--}}

@use('App\Enums\InsuranceStatus')

@php
    $units = fn (?int $cents): string => $cents === null ? '' : number_format($cents / 100, 2, '.', '');
    $selectedCurrency = $record?->currency_code ?? $collection->currency ?? array_key_first($currencies);
    $inputClasses = 'mt-1.5 h-10 w-full rounded-lg border border-hairline bg-input px-3 text-sm text-ink';
    $labelClasses = 'block text-xs font-semibold text-muted-soft';
    $optional = '<span class="font-medium text-muted-soft/70">— '.__('optional').'</span>';
@endphp

<x-form
  :method="$method"
  :action="$action"
  :data-test="$dataTest"
  class="overflow-hidden rounded-xl border border-hairline"
  x-data="{ provider: '', status: '{{ $record?->status->value ?? InsuranceStatus::Active->value }}', scheduled: {{ $record?->is_scheduled_item ? 'true' : 'false' }} }"
  x-target="history-panel notifications"
  x-on:ajax:after="{{ $openVar }} = document.querySelector('#{{ $formId }}-fields .text-error') !== null"
>
  {{-- Header --}}
  <div class="flex items-center gap-2.5 border-b border-hairline bg-card/40 px-5 py-3">
    <span class="size-2.5 shrink-0 rounded-full" style="background-color: #8b5cf6"></span>
    <span class="text-sm font-semibold text-ink">{{ $formTitle }}</span>
    <span class="flex-1"></span>
    <button type="button" x-on:click="{{ $openVar }} = false" class="text-[13px] font-semibold text-muted-soft hover:text-ink">{{ __('Cancel') }}</button>
  </div>

  {{-- Body --}}
  <div id="{{ $formId }}-fields" class="p-5">
    <input type="hidden" name="status" x-bind:value="status" />
    <input type="hidden" name="is_scheduled_item" x-bind:value="scheduled ? '1' : '0'" />

    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-[1.3fr_1fr]">
      <div>
        <label for="{{ $formId }}-provider" class="{{ $labelClasses }}">{{ __('Provider') }}</label>
        <input id="{{ $formId }}-provider" name="provider" value="{{ $record?->provider }}" x-init="provider = $el.value" x-on:input="provider = $event.target.value" placeholder="{{ __('Insurer name') }}" class="{{ $inputClasses }}" data-test="{{ $formId }}-provider" />
        <x-error :messages="$errors->get('provider')" class="mt-2" />
      </div>

      <div>
        <label class="{{ $labelClasses }}">{{ __('Status') }}</label>
        <div class="mt-1.5 flex gap-1.5">
          @foreach (InsuranceStatus::cases() as $case)
            @php $color = $case->color(); @endphp
            <button
              type="button"
              x-on:click="status = '{{ $case->value }}'"
              class="flex-1 rounded-lg border px-2 py-2.5 text-[12.5px] font-semibold capitalize transition"
              x-bind:class="status === '{{ $case->value }}' ? 'border-transparent' : 'border-hairline text-muted hover:bg-card'"
              x-bind:style="status === '{{ $case->value }}' ? 'background-color: {{ $color }}24; color: {{ $color }}; border-color: {{ $color }}80' : ''"
              data-test="{{ $formId }}-status-{{ $case->value }}"
            >{{ $case->label() }}</button>
          @endforeach
        </div>
        <x-error :messages="$errors->get('status')" class="mt-2" />
      </div>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div>
        <label for="{{ $formId }}-policy-number" class="{{ $labelClasses }}">{{ __('Policy #') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-policy-number" name="policy_number" value="{{ $record?->policy_number }}" placeholder="{{ __('Policy number') }}" class="{{ $inputClasses }} font-mono" />
        <x-error :messages="$errors->get('policy_number')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-coverage-type" class="{{ $labelClasses }}">{{ __('Coverage type') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-coverage-type" name="coverage_type" value="{{ $record?->coverage_type }}" placeholder="{{ __('Scheduled item, blanket…') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('coverage_type')" class="mt-2" />
      </div>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div>
        <label for="{{ $formId }}-insured-value" class="{{ $labelClasses }}">{{ __('Insured value') }}</label>
        <div class="mt-1.5 flex h-10 items-stretch overflow-hidden rounded-lg border border-hairline bg-input">
          <select name="currency" class="border-0 border-r border-hairline bg-card px-3 text-sm font-semibold text-ink focus:ring-0">
            @foreach ($currencies as $code => $label)
              <option value="{{ $code }}" @selected($code === $selectedCurrency)>{{ $code }}</option>
            @endforeach
          </select>
          <input id="{{ $formId }}-insured-value" name="insured_value" type="number" step="0.01" min="0" value="{{ $units($record?->insured_value) }}" placeholder="0.00" class="min-w-0 flex-1 border-0 bg-transparent px-3.5 text-base font-semibold text-ink focus:ring-0" data-test="{{ $formId }}-insured-value" />
        </div>
        <x-error :messages="$errors->get('insured_value')" class="mt-2" />
        <x-error :messages="$errors->get('currency')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-deductible-amount" class="{{ $labelClasses }}">{{ __('Deductible') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-deductible-amount" name="deductible_amount" type="number" step="0.01" min="0" value="{{ $units($record?->deductible_amount) }}" placeholder="0.00" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('deductible_amount')" class="mt-2" />
      </div>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div>
        <label for="{{ $formId }}-starts-at" class="{{ $labelClasses }}">{{ __('Starts') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-starts-at" name="starts_at" type="date" value="{{ $record?->starts_at?->toDateString() }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('starts_at')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-ends-at" class="{{ $labelClasses }}">{{ __('Ends') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-ends-at" name="ends_at" type="date" value="{{ $record?->ends_at?->toDateString() }}" placeholder="{{ __('Leave blank if ongoing') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('ends_at')" class="mt-2" />
      </div>
    </div>

    <button type="button" x-on:click="scheduled = ! scheduled" class="mb-4 flex w-full items-center gap-3 rounded-lg border border-hairline px-3.5 py-3 text-left" data-test="{{ $formId }}-scheduled-toggle">
      <span class="relative h-[22px] w-[38px] shrink-0 rounded-full transition-colors" x-bind:style="scheduled ? 'background-color: #8b5cf6' : 'background-color: #d1d5db'">
        <span class="absolute top-0.5 size-[18px] rounded-full bg-white shadow transition-all" x-bind:style="scheduled ? 'left: 18px' : 'left: 2px'"></span>
      </span>
      <span class="min-w-0">
        <span class="block text-[13.5px] font-semibold text-ink">{{ __('Individually scheduled item') }}</span>
        <span class="block text-xs text-muted-soft">{{ __('This copy is listed on the policy by name, not under blanket contents.') }}</span>
      </span>
    </button>

    <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div>
        <label for="{{ $formId }}-contact-name" class="{{ $labelClasses }}">{{ __('Contact') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-contact-name" name="contact_name" value="{{ $record?->contact_name }}" placeholder="{{ __('Agent name') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('contact_name')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-contact-email" class="{{ $labelClasses }}">{{ __('Email') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-contact-email" name="contact_email" type="email" value="{{ $record?->contact_email }}" placeholder="{{ __('agent@insurer.com') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('contact_email')" class="mt-2" />
      </div>

      <div>
        <label for="{{ $formId }}-contact-phone" class="{{ $labelClasses }}">{{ __('Phone') }} {!! $optional !!}</label>
        <input id="{{ $formId }}-contact-phone" name="contact_phone" value="{{ $record?->contact_phone }}" placeholder="{{ __('+1 …') }}" class="{{ $inputClasses }}" />
        <x-error :messages="$errors->get('contact_phone')" class="mt-2" />
      </div>
    </div>

    <div>
      <label for="{{ $formId }}-note" class="{{ $labelClasses }}">{{ __('Note') }} {!! $optional !!}</label>
      <textarea id="{{ $formId }}-note" name="note" rows="2" placeholder="{{ __('Anything worth recording about this coverage.') }}" class="mt-1.5 w-full rounded-lg border border-hairline bg-input px-3 py-2.5 text-sm text-ink">{{ $record?->note }}</textarea>
      <x-error :messages="$errors->get('note')" class="mt-2" />
    </div>
  </div>

  {{-- Footer --}}
  <div class="flex flex-wrap items-center justify-between gap-3 border-t border-hairline bg-card/40 px-5 py-3.5">
    <p class="text-[13px] text-muted-soft">{{ $hint }}</p>

    <div class="flex items-center gap-2.5">
      <x-button.secondary type="button" x-on:click="{{ $openVar }} = false" class="!h-9 !px-4 text-[13px]">
        {{ __('Cancel') }}
      </x-button.secondary>

      <x-button type="submit" class="!h-9 text-[13px]" x-bind:disabled="! provider.trim()" x-bind:class="! provider.trim() ? 'opacity-40' : ''" data-test="{{ $formId }}-submit">
        {{ $submitLabel }}
      </x-button>
    </div>
  </div>
</x-form>
