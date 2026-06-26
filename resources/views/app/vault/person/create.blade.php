<x-app-layout :vault="$vault">
  <x-slot:title>
    {{ __('app/person.new.title') }}
  </x-slot:title>

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-xl items-start justify-center">
      <x-box title="{{ __('app/person.new.title') }}">
        <x-form method="post" :action="route('vault.person.create', $vault->id)" class="space-y-4">
          <div
            class="space-y-4"
            x-data="{
              showPrefix: {{ $errors->has('prefix') || old('prefix') ? 'true' : 'false' }},
              showMiddleName:
                {{ $errors->has('middle_name') || old('middle_name') ? 'true' : 'false' }},
              showSuffix: {{ $errors->has('suffix') || old('suffix') ? 'true' : 'false' }},
              showNickname:
                {{ $errors->has('nickname') || old('nickname') ? 'true' : 'false' }},
              showMaidenName:
                {{ $errors->has('maiden_name') || old('maiden_name') ? 'true' : 'false' }},
              showGender:
                {{ $errors->has('gender_id') || old('gender_id') ? 'true' : 'false' }},
              showKids:
                {{ $errors->has('kids_status') || old('kids_status') ? 'true' : 'false' }},
              selectedKidsStatus: @js(__('app/person.options.' . (old('kids_status') ?: 'unknown'))),
            }">
            <div x-cloak x-show="showPrefix" x-transition class="relative">
              <x-input id="prefix" :label="__('app/person.fields.prefix')" :help="__('app/person.help.prefix')" :error="$errors->get('prefix')" :value="old('prefix')" x-ref="prefix" />
            </div>

            <div class="relative">
              <x-input id="first_name" :label="__('app/person.fields.first_name')" :error="$errors->get('first_name')" :value="old('first_name')" required autofocus />
            </div>

            <div class="relative">
              <x-input id="last_name" :label="__('app/person.fields.last_name')" :error="$errors->get('last_name')" :value="old('last_name')" />
            </div>

            <div x-on:click="showKids = !showKids" class="flex cursor-pointer items-center justify-between text-sm font-medium text-gray-700 dark:text-gray-300">
              <div class="flex items-center gap-x-2">
                <x-phosphor-baby class="h-4 w-4 text-blue-500" />
                <span>{{ __('app/person.fields.kids_status') }}</span>
              </div>

              <div class="flex items-center gap-x-1">
                <p class="text-xs text-gray-500" x-text="selectedKidsStatus"></p>
                <x-phosphor-caret-down x-show="!showKids" class="size-4 transition" />
                <x-phosphor-caret-up x-show="showKids" class="size-4 transition" />
              </div>
            </div>

            <x-error class="mt-2" :messages="$errors->get('kids_status')" />

            <div x-cloak x-show="showKids" x-transition class="rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-blue-900">
              @foreach (['' => 'unknown', 'no_kids' => 'no_kids', 'maybe_kids' => 'maybe_kids', 'has_kids' => 'has_kids'] as $value => $label)
                <div class="flex items-center gap-x-3 p-3 not-last:border-b not-last:border-gray-200 dark:not-last:border-gray-700">
                  <input id="kids-status-{{ $label }}" value="{{ $value }}" name="kids_status" type="radio" @checked (old('kids_status', '') === $value) x-on:click="selectedKidsStatus = @js(__('app/person.options.' . $label))" class="relative size-4 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 forced-colors:appearance-auto forced-colors:before:hidden" />
                  <label for="kids-status-{{ $label }}" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-100">{{ __('app/person.options.' . $label) }}</label>
                </div>
              @endforeach
            </div>

            <div x-cloak x-show="showMiddleName" x-transition class="relative">
              <x-input id="middle_name" :label="__('app/person.fields.middle_name')" :error="$errors->get('middle_name')" :value="old('middle_name')" x-ref="middleName" />
            </div>

            <div x-cloak x-show="showNickname" x-transition class="relative">
              <x-input id="nickname" :label="__('app/person.fields.nickname')" :error="$errors->get('nickname')" :value="old('nickname')" x-ref="nickname" />
            </div>

            <div x-cloak x-show="showMaidenName" x-transition class="relative">
              <x-input id="maiden_name" :label="__('app/person.fields.maiden_name')" :help="__('app/person.help.maiden_name')" :error="$errors->get('maiden_name')" :value="old('maiden_name')" x-ref="maidenName" />
            </div>

            <div x-cloak x-show="showSuffix" x-transition class="relative">
              <x-input id="suffix" :label="__('app/person.fields.suffix')" :help="__('app/person.help.suffix')" :error="$errors->get('suffix')" :value="old('suffix')" x-ref="suffix" />
            </div>

            <div x-cloak x-show="showGender" x-transition class="relative">
              <x-select id="gender_id" :label="__('app/person.fields.gender')" :options="$genders" :selected="old('gender_id')" :error="$errors->get('gender_id')" />
            </div>

            <div class="flex flex-wrap text-xs">
              @foreach ([['state' => 'showPrefix', 'ref' => 'prefix', 'label' => 'prefix'], ['state' => 'showMiddleName', 'ref' => 'middleName', 'label' => 'middle_name'], ['state' => 'showSuffix', 'ref' => 'suffix', 'label' => 'suffix'], ['state' => 'showNickname', 'ref' => 'nickname', 'label' => 'nickname'], ['state' => 'showMaidenName', 'ref' => 'maidenName', 'label' => 'maiden_name']] as $field)
                <button
                  type="button"
                  x-cloak
                  x-show="! {{ $field['state'] }}"
                  class="me-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border border-slate-300 bg-slate-200 px-1 py-1 hover:bg-slate-300 dark:border-gray-600 dark:bg-slate-500 dark:hover:bg-slate-400"
                  x-on:click="
                  {{ $field['state'] }} = true
                  $nextTick(() => $refs.{{ $field['ref'] }}.focus())
                ">
                  {{ __('app/person.add_field.' . $field['label']) }}
                </button>
              @endforeach

              @if ($genders->count() > 1)
                <button type="button" x-cloak x-show="!showGender" x-on:click="showGender = true" class="me-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border border-slate-300 bg-slate-200 px-1 py-1 hover:bg-slate-300 dark:border-gray-600 dark:bg-slate-500 dark:hover:bg-slate-400">{{ __('app/person.add_field.gender') }}</button>
              @endif
            </div>
          </div>

          <div class="flex justify-between">
            <x-button.secondary href="{{ route('vault.person.index', $vault->id) }}" turbo="true">{{ __('app/shared.cancel') }}</x-button.secondary>

            <x-button type="submit">{{ __('app/shared.save') }}</x-button>
          </div>
        </x-form>
      </x-box>
    </div>
  </div>
</x-app-layout>
