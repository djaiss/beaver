@use('App\Enums\FieldTypeEnum')

<x-app-layout>
  <x-slot:title>
    {{ $type->name !== '' ? $type->name : __('Untitled type') }}
  </x-slot>

  {{-- Turbo morphs the page in place after each save, so edits feel instant and scroll is kept. --}}
  <x-slot:head>
    <meta name="turbo-refresh-method" content="morph" />
    <meta name="turbo-refresh-scroll" content="preserve" />
  </x-slot>

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div class="mx-auto w-full max-w-3xl">
      {{-- Breadcrumb --}}
      <div class="mb-6 flex items-center gap-2 text-xs text-muted-soft">
        @if (auth()->user()->isOwner())
          <a href="{{ route('settings.index') }}" data-turbo="true" class="font-medium transition-colors hover:text-ink">{{ __('Account settings') }}</a>
        @else
          <span>{{ __('Account settings') }}</span>
        @endif
        <span>/</span>
        <a href="{{ route('settings.types.index') }}" data-turbo="true" class="font-medium transition-colors hover:text-ink">{{ __('Collection types') }}</a>
        <span>/</span>
        <span class="font-medium text-ink">{{ $type->name !== '' ? $type->name : __('Untitled type') }}</span>
      </div>

      {{-- Header: color, name, delete --}}
      <div class="mb-6 flex items-start gap-5">
        <div class="flex shrink-0 flex-col items-center gap-2.5">
          <span class="size-14 rounded-full" style="background-color: {{ $type->color }}"></span>

          <x-form method="put" :action="route('settings.types.update', $type->id)" data-turbo="true" class="flex gap-1.5">
            <input type="hidden" name="name" value="{{ $type->name }}" />
            @foreach ($palette as $swatch)
              <button
                type="submit"
                name="color"
                value="{{ $swatch }}"
                aria-label="{{ $swatch }}"
                class="size-4 cursor-pointer rounded-full ring-offset-1 ring-offset-canvas {{ $type->color === $swatch ? 'ring-2 ring-ink' : '' }}"
                style="background-color: {{ $swatch }}"
              ></button>
            @endforeach
          </x-form>
        </div>

        <div class="min-w-0 flex-1">
          <div id="name-display">
            <h1 class="truncate text-2xl font-semibold tracking-tight text-ink">{{ $type->name !== '' ? $type->name : __('Untitled type') }}</h1>
          </div>

          <x-form id="name-edit" hidden method="put" :action="route('settings.types.update', $type->id)" data-turbo="true" class="flex items-center gap-2">
            <input type="hidden" name="color" value="{{ $type->color }}" />
            <input
              id="type-name-input"
              name="name"
              value="{{ $type->name }}"
              placeholder="{{ __('Type name') }}"
              class="w-full max-w-xs rounded-md border border-hairline bg-input px-3 py-2 text-lg font-semibold text-ink"
            />
            <x-button type="submit" data-test="save-name-button">{{ __('Save') }}</x-button>
            <button
              type="button"
              onclick="document.getElementById('name-edit').hidden=true;document.getElementById('name-display').hidden=false;document.getElementById('edit-name-button').hidden=false"
              class="cursor-pointer text-sm font-semibold text-muted hover:text-ink"
            >{{ __('Cancel') }}</button>
          </x-form>

          <p class="mt-2 text-xs text-muted-soft">{{ __('Used by :collections collection(s) · :fields custom field(s)', ['collections' => $type->collections->count(), 'fields' => $type->customFields->count()]) }}</p>
        </div>

        <div class="flex shrink-0 items-center gap-3">
          <x-button
            type="button"
            id="edit-name-button"
            data-test="edit-name-button"
            onclick="document.getElementById('name-display').hidden=true;document.getElementById('name-edit').hidden=false;document.getElementById('type-name-input').focus();this.hidden=true"
          >{{ __('Edit name') }}</x-button>

          <x-form method="delete" :action="route('settings.types.destroy', $type->id)" data-turbo="true" onsubmit="return confirm('{{ __('Delete this type? This cannot be undone.') }}')">
            <x-button.secondary type="submit" data-test="delete-type-button" class="border-error/40 text-error hover:bg-error/5">{{ __('Delete type') }}</x-button.secondary>
          </x-form>
        </div>
      </div>

      {{-- Everything below the header auto-saves; make that explicit. --}}
      <div class="mb-9 flex items-center gap-2.5 rounded-lg border border-hairline bg-card px-4 py-3 text-sm text-muted">
        @svg('lucide-info', 'size-4 shrink-0 text-muted-soft')
        <span>{{ __('Changes below are saved automatically in real time as you make them. No need to hit save.') }}</span>
      </div>

      {{-- Custom fields --}}
      <div class="mb-3.5 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-ink">{{ __('Custom fields') }}</h2>

        <x-form method="post" :action="route('settings.types.fields.create', $type->id)" data-turbo="true">
          <button type="submit" data-test="add-field-button" class="cursor-pointer rounded-md border border-dashed border-hairline px-3 py-2 text-xs font-semibold text-ink transition-colors hover:bg-card">+ {{ __('Add field') }}</button>
        </x-form>
      </div>

      <div class="mb-10 flex flex-col gap-3">
        @forelse ($type->customFields as $field)
          <div id="field-row-{{ $field->id }}" class="rounded-xl border border-hairline bg-canvas p-4" data-test="field-row-{{ $field->id }}">
            {{-- Separate action forms for reorder and delete; their buttons live in the row below via the form= attribute. --}}
            <x-form method="put" :action="route('settings.types.fields.order.update', [$type->id, $field->id])" id="field-up-{{ $field->id }}" data-turbo="true" class="hidden">
              <input type="hidden" name="direction" value="up" />
            </x-form>
            <x-form method="put" :action="route('settings.types.fields.order.update', [$type->id, $field->id])" id="field-down-{{ $field->id }}" data-turbo="true" class="hidden">
              <input type="hidden" name="direction" value="down" />
            </x-form>
            <x-form method="delete" :action="route('settings.types.fields.destroy', [$type->id, $field->id])" id="field-delete-{{ $field->id }}" data-turbo="true" class="hidden" onsubmit="return confirm('{{ __('Delete this custom field? The data stored in it on every item will be permanently deleted. This cannot be undone.') }}')"></x-form>

            <x-form method="put" :action="route('settings.types.fields.update', [$type->id, $field->id])" data-turbo="true" onchange="this.requestSubmit()">
              {{-- Top row: name, type, reorder, delete. Stays fixed even when options appear below. --}}
              <div class="flex items-end gap-2.5">
                <div class="min-w-0 flex-1">
                  <label class="mb-1.5 block text-xs font-semibold text-muted-soft">{{ __('Field name') }}</label>
                  <input name="name" value="{{ $field->name }}" placeholder="{{ __('e.g. Issue #') }}" data-test="field-name-{{ $field->id }}" class="h-10 w-full rounded-md border border-hairline bg-input px-3 text-sm text-ink" />
                </div>

                <div class="w-40 shrink-0">
                  <label class="mb-1.5 block text-xs font-semibold text-muted-soft">{{ __('Field type') }}</label>
                  <select name="field_type" class="h-10 w-full appearance-none rounded-md border border-hairline bg-input pl-3 pr-9 text-sm text-ink">
                    @foreach ($fieldTypes as $value => $label)
                      <option value="{{ $value }}" @selected($field->field_type->value === $value)>{{ $label }}</option>
                    @endforeach
                  </select>
                </div>

                <div class="flex shrink-0 flex-col">
                  <button type="submit" form="field-up-{{ $field->id }}" aria-label="{{ __('Move up') }}" data-test="move-up-{{ $field->id }}" class="flex h-[19px] w-8 cursor-pointer items-center justify-center rounded-t-md border border-hairline text-[10px] text-muted hover:bg-card">▲</button>
                  <button type="submit" form="field-down-{{ $field->id }}" aria-label="{{ __('Move down') }}" data-test="move-down-{{ $field->id }}" class="flex h-[19px] w-8 cursor-pointer items-center justify-center rounded-b-md border border-t-0 border-hairline text-[10px] text-muted hover:bg-card">▼</button>
                </div>

                <button type="submit" form="field-delete-{{ $field->id }}" aria-label="{{ __('Remove field') }}" data-test="delete-field-{{ $field->id }}" class="flex size-10 shrink-0 cursor-pointer items-center justify-center rounded-md border border-hairline text-muted hover:bg-card">×</button>
              </div>

              {{-- Options for a select field, rendered below the top row. --}}
              @if ($field->field_type === FieldTypeEnum::Select)
                <div class="mt-3.5 border-t border-hairline-soft pt-3.5">
                  <label class="mb-2 block text-xs font-semibold text-muted-soft">{{ __('Options') }}</label>

                  <div class="mb-2.5 flex flex-col gap-1.5">
                    @foreach ($field->options ?? [] as $option)
                      <div class="flex items-center gap-2" data-opt-row>
                        <input type="hidden" name="options[]" value="{{ $option }}" />
                        <div class="flex-1 rounded-md bg-card px-3 py-2 text-sm font-medium text-ink">{{ $option }}</div>
                        <button
                          type="button"
                          aria-label="{{ __('Remove option') }}"
                          data-test="remove-option-{{ $loop->index }}"
                          onclick="if(!confirm('{{ __('Delete this option? Any item set to it will lose that value. This cannot be undone.') }}'))return;const f=this.form;this.closest('[data-opt-row]').remove();f.requestSubmit()"
                          class="flex size-8 cursor-pointer items-center justify-center rounded-md text-muted hover:text-ink"
                        >×</button>
                      </div>
                    @endforeach
                  </div>

                  <div class="flex gap-2">
                    <input name="options[]" value="" placeholder="{{ __('Add option…') }}" data-test="option-draft" class="h-9 flex-1 rounded-md border border-hairline bg-input px-3 text-sm text-ink" />
                    <button type="submit" data-test="add-option-button" class="h-9 cursor-pointer rounded-md bg-card px-3.5 text-sm font-semibold text-ink">{{ __('Add') }}</button>
                  </div>
                </div>
              @endif
            </x-form>
          </div>
        @empty
          <div class="rounded-xl border border-dashed border-hairline p-8 text-center text-sm text-muted">{{ __('No custom fields yet. Add one above.') }}</div>
        @endforelse
      </div>

      {{-- Collections using this type --}}
      <h2 class="text-lg font-semibold text-ink">{{ __('Collections using this type') }}</h2>
      <p class="mt-0.5 mb-3.5 text-xs text-muted-soft">{{ __('A collection can use many types; an item picks exactly one.') }}</p>

      @if ($collections->isEmpty())
        <p class="text-sm text-muted">{{ __('No collections yet.') }}</p>
      @else
        <x-form method="put" :action="route('settings.types.collections.update', $type->id)" data-turbo="true" onchange="this.requestSubmit()" class="flex flex-wrap gap-2">
          @foreach ($collections as $collection)
            @php($checked = $type->collections->contains($collection->id))
            <label class="flex cursor-pointer items-center gap-2 rounded-full border px-3.5 py-2 text-sm font-medium text-ink transition-colors {{ $checked ? 'border-ink bg-card' : 'border-hairline hover:bg-card' }}">
              <input type="checkbox" name="collection_ids[]" value="{{ $collection->id }}" @checked($checked) class="sr-only" />
              <span>{{ $collection->emoji }}</span>
              {{ $collection->name }}
            </label>
          @endforeach
        </x-form>
      @endif
    </div>
  </div>
</x-app-layout>
