<x-app-layout :collection="$collection">
  <x-slot:title>
    {{ __('Add an item') }}
  </x-slot>

  @php
    $typesData = $types->map(fn ($type) => [
      'id' => $type->id,
      'fields' => $type->customFields->map(fn ($field) => [
        'id' => $field->id,
        'name' => $field->name,
        'type' => $field->field_type->value,
        'options' => $field->options ?? [],
      ])->values(),
    ])->values();
  @endphp

  <div class="px-6 py-8 lg:px-12 lg:py-10">
    <div
      class="mx-auto w-full max-w-2xl"
      x-data="{
        types: @js($typesData),
        name: @js(old('name', '')),
        typeId: @js(old('type_id', '')),
        customValues: {},
        selectedTagIds: [],
        newTags: [],
        tagDraft: '',
        copies: [{ condition_id: '', location_id: '', acquired_at: '', price_paid: '', estimated_value: '' }],
        coverName: '',
        coverPreview: '',
        dragging: false,
        get selectedType() { return this.types.find(t => String(t.id) === String(this.typeId)) || null },
        get customFields() { return this.selectedType ? this.selectedType.fields : [] },
        get canSubmit() { return this.name.trim().length > 0 },
        toggleType(id) { this.typeId = String(this.typeId) === String(id) ? '' : String(id) },
        isTypeActive(id) { return String(this.typeId) === String(id) },
        toggleTag(id) { const i = this.selectedTagIds.indexOf(id); i === -1 ? this.selectedTagIds.push(id) : this.selectedTagIds.splice(i, 1) },
        addTag() { const v = this.tagDraft.trim(); if (v && !this.newTags.includes(v)) this.newTags.push(v); this.tagDraft = '' },
        removeNewTag(name) { this.newTags = this.newTags.filter(t => t !== name) },
        addCopy() { this.copies.push({ condition_id: '', location_id: '', acquired_at: '', price_paid: '', estimated_value: '' }) },
        removeCopy(i) { if (this.copies.length > 1) this.copies.splice(i, 1) },
        setCover(file) {
          if (!file || !file.type.startsWith('image/')) return
          this.coverName = file.name
          this.coverPreview = URL.createObjectURL(file)
        },
        onCover(e) { this.setCover(e.target.files[0]) },
        onDropCover(e) {
          this.dragging = false
          const file = e.dataTransfer.files[0]
          if (!file || !file.type.startsWith('image/')) return
          // Assign the dropped file to the input so it submits with the form.
          this.$refs.coverInput.files = e.dataTransfer.files
          this.setCover(file)
        },
      }"
    >
      <div class="mb-5 flex items-center gap-1.5 text-[13px]">
        <a href="{{ route('collections.show', $collection) }}" data-turbo="true" class="font-medium text-muted-soft transition-colors hover:text-ink">{{ $collection->name }}</a>
        <span class="text-muted-soft">/</span>
        <span class="font-medium text-ink">{{ __('New item') }}</span>
      </div>

      <div class="mb-8">
        <h1 class="text-[28px] font-semibold tracking-tight text-ink">{{ __('Add an item') }}</h1>
        <p class="mt-1.5 text-[15px] text-muted">{{ __('Catalog details apply to the item; each physical copy tracks its own condition and location.') }}</p>
      </div>

      <x-form method="post" :action="route('items.create', $collection)" :upload="true" data-turbo="true" data-test="add-item-form" class="space-y-6">
        {{-- Cover --}}
        <div>
          <x-label>{{ __('Cover image') }}</x-label>
          <label
            class="relative mt-2 flex size-24 cursor-pointer items-center justify-center overflow-hidden rounded-xl border border-dashed bg-card text-center transition-colors"
            :class="dragging ? 'border-muted-soft ring-2 ring-[var(--color-accent)]/40' : 'border-hairline hover:border-muted-soft'"
            x-on:dragover.prevent="dragging = true"
            x-on:dragleave.prevent="dragging = false"
            x-on:drop.prevent="onDropCover($event)"
          >
            <input x-ref="coverInput" type="file" name="cover" accept="image/*" class="sr-only" x-on:change="onCover($event)" />
            <img x-show="coverPreview" x-cloak :src="coverPreview" alt="" class="pointer-events-none absolute inset-0 size-full object-cover" />
            <span x-show="!coverPreview" class="pointer-events-none px-2 font-mono text-[10px] leading-tight text-muted-soft">{{ __('Drop or click') }}</span>
          </label>
          <p x-show="coverName" x-cloak x-text="coverName" class="mt-1.5 truncate text-[11px] text-muted-soft"></p>
          <x-error :messages="$errors->get('cover')" class="mt-2" />
        </div>

        {{-- Name --}}
        <x-input id="name" :label="__('Name')" placeholder="{{ __('e.g. Amazing Spider-Man #1') }}" x-model="name" :error="$errors->get('name')" required autofocus data-test="item-name-input" />

        {{-- Description --}}
        <x-textarea id="description" :label="__('Description')" placeholder="{{ __('Notes about this item…') }}" rows="3" :value="old('description')" :error="$errors->get('description')" />

        {{-- Type --}}
        <div>
          <x-label>{{ __('Type') }} <span class="font-normal text-muted-soft">({{ __('Optional') }})</span></x-label>
          <p class="mt-0.5 mb-2.5 text-[13px] text-muted-soft">{{ __('Choosing a type unlocks its custom fields below.') }}</p>
          <input type="hidden" name="type_id" :value="typeId" />
          <div class="flex flex-wrap gap-2">
            @foreach ($types as $type)
              <div
                x-on:click="toggleType('{{ $type->id }}')"
                class="flex cursor-pointer items-center gap-2 rounded-full border px-3.5 py-2 text-sm font-medium text-ink transition-colors"
                :class="isTypeActive('{{ $type->id }}') ? 'border-ink bg-card' : 'border-hairline'"
              >
                <span class="size-2 shrink-0 rounded-full" style="background-color: {{ $type->color }}"></span>
                {{ $type->name }}
              </div>
            @endforeach
          </div>
        </div>

        {{-- Custom fields --}}
        <div x-show="customFields.length > 0" x-cloak class="rounded-xl border border-hairline bg-canvas p-5">
          <p class="mb-4 text-[13px] font-semibold tracking-wide text-muted-soft uppercase">{{ __('Type fields') }}</p>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <template x-for="field in customFields" :key="field.id">
              <div>
                <label class="mb-2 block text-[13px] font-semibold text-ink" x-text="field.name"></label>

                {{-- Boolean --}}
                <template x-if="field.type === 'boolean'">
                  <label class="flex h-11 cursor-pointer items-center gap-2.5">
                    <input type="checkbox" value="1" class="sr-only" :name="`custom_fields[${field.id}]`" x-model="customValues[field.id]" />
                    <span class="relative h-6 w-10 shrink-0 rounded-full transition-colors" :class="customValues[field.id] ? 'bg-ink' : 'bg-hairline'">
                      <span class="absolute top-[3px] size-[18px] rounded-full bg-white transition-all" :class="customValues[field.id] ? 'left-[19px]' : 'left-[3px]'"></span>
                    </span>
                    <span class="text-[13px] text-muted" x-text="customValues[field.id] ? '{{ __('Yes') }}' : '{{ __('No') }}'"></span>
                  </label>
                </template>

                {{-- Select --}}
                <template x-if="field.type === 'select'">
                  <select :name="`custom_fields[${field.id}]`" x-model="customValues[field.id]" class="h-11 w-full appearance-none rounded-md border border-hairline bg-input pl-3 pr-9 text-sm text-ink">
                    <option value="">{{ __('Select…') }}</option>
                    <template x-for="option in field.options" :key="option">
                      <option :value="option" x-text="option"></option>
                    </template>
                  </select>
                </template>

                {{-- Rating --}}
                <template x-if="field.type === 'rating'">
                  <div class="flex h-11 items-center gap-1">
                    <input type="hidden" :name="`custom_fields[${field.id}]`" x-model="customValues[field.id]" />

                    <template x-for="star in {{ App\Enums\FieldTypeEnum::MAX_RATING }}" :key="star">
                      <button
                        type="button"
                        x-on:click="customValues[field.id] = customValues[field.id] == star ? '' : star"
                        class="cursor-pointer text-xl leading-none"
                        :class="customValues[field.id] >= star ? 'text-ink' : 'text-hairline'"
                        :aria-label="`${star}`"
                      >
                        ★
                      </button>
                    </template>

                    <button
                      type="button"
                      x-show="customValues[field.id]"
                      x-cloak
                      x-on:click="customValues[field.id] = ''"
                      class="ml-2 cursor-pointer text-[13px] text-muted hover:text-ink"
                    >
                      {{ __('Clear') }}
                    </button>
                  </div>
                </template>

                {{-- Text / number / date --}}
                <template x-if="field.type !== 'boolean' && field.type !== 'select' && field.type !== 'rating'">
                  <input
                    :type="field.type === 'number' ? 'number' : field.type === 'date' ? 'date' : 'text'"
                    :name="`custom_fields[${field.id}]`"
                    x-model="customValues[field.id]"
                    class="h-11 w-full rounded-md border border-hairline bg-input px-3.5 text-sm text-ink placeholder-muted-soft"
                  />
                </template>
              </div>
            </template>
          </div>
        </div>

        {{-- Category & set --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <x-label for="category_id">{{ __('Category') }} <span class="font-normal text-muted-soft">({{ __('Optional') }})</span></x-label>
            <select id="category_id" name="category_id" class="mt-2 h-11 w-full appearance-none rounded-md border border-hairline bg-input pl-3 pr-9 text-sm text-ink">
              <option value="">{{ __('No category') }}</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <x-label for="set_id">{{ __('Set') }} <span class="font-normal text-muted-soft">({{ __('Optional') }})</span></x-label>
            <select id="set_id" name="set_id" class="mt-2 h-11 w-full appearance-none rounded-md border border-hairline bg-input pl-3 pr-9 text-sm text-ink">
              <option value="">{{ __('No set') }}</option>
              @foreach ($sets as $set)
                <option value="{{ $set->id }}" @selected(old('set_id') == $set->id)>{{ $set->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Tags --}}
        <div>
          <x-label>{{ __('Tags') }} <span class="font-normal text-muted-soft">({{ __('Optional') }})</span></x-label>
          <div class="mt-2.5 flex flex-wrap items-center gap-2">
            @foreach ($tags as $tag)
              <div
                x-on:click="toggleTag({{ $tag->id }})"
                class="flex cursor-pointer items-center gap-1.5 rounded-full border px-3.5 py-2 text-[13px] font-medium text-ink transition-colors"
                :class="selectedTagIds.includes({{ $tag->id }}) ? 'border-ink bg-card' : 'border-hairline'"
              >{{ $tag->name }}</div>
            @endforeach

            <template x-for="name in newTags" :key="name">
              <div x-on:click="removeNewTag(name)" class="flex cursor-pointer items-center gap-1.5 rounded-full border border-ink bg-card px-3.5 py-2 text-[13px] font-medium text-ink">
                <span x-text="name"></span>
                <span class="text-muted-soft">&times;</span>
              </div>
            </template>

            <input
              x-model="tagDraft"
              x-on:keydown.enter.prevent="addTag()"
              placeholder="{{ __('Add tag + Enter') }}"
              class="h-9 w-36 rounded-full border border-dashed border-hairline bg-transparent px-3.5 text-[13px] text-ink placeholder-muted-soft"
            />
          </div>

          <template x-for="id in selectedTagIds" :key="id">
            <input type="hidden" name="tag_ids[]" :value="id" />
          </template>
          <template x-for="name in newTags" :key="name">
            <input type="hidden" name="new_tags[]" :value="name" />
          </template>
        </div>

        <div class="h-px bg-hairline-soft"></div>

        {{-- Copies --}}
        <div>
          <h2 class="text-lg font-semibold text-ink">{{ __('Copies') }}</h2>
          <p class="mt-0.5 text-[13px] text-muted-soft">{{ __('Each copy is a physical instance you own — add one row per copy.') }}</p>
        </div>

        <div class="space-y-3">
          <template x-for="(copy, index) in copies" :key="index">
            <div class="rounded-xl border border-hairline bg-canvas p-4">
              <div class="mb-3.5 flex items-center justify-between">
                <span class="text-[13px] font-semibold text-muted-soft">{{ __('Copy') }} <span x-text="index + 1"></span></span>
                <button
                  type="button"
                  x-show="copies.length > 1"
                  x-on:click="removeCopy(index)"
                  class="flex size-7 items-center justify-center rounded-lg border border-hairline text-muted transition-colors hover:bg-card"
                  aria-label="{{ __('Remove copy') }}"
                >&times;</button>
              </div>

              <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-xs font-semibold text-muted-soft">{{ __('Condition') }}</label>
                  <select :name="`copies[${index}][condition_id]`" x-model="copy.condition_id" class="h-10 w-full appearance-none rounded-md border border-hairline bg-input pl-3 pr-9 text-sm text-ink">
                    <option value="">{{ __('Not set') }}</option>
                    @foreach ($conditions as $condition)
                      <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="mb-1.5 block text-xs font-semibold text-muted-soft">{{ __('Location') }}</label>
                  <select :name="`copies[${index}][location_id]`" x-model="copy.location_id" class="h-10 w-full appearance-none rounded-md border border-hairline bg-input pl-3 pr-9 text-sm text-ink">
                    <option value="">{{ __('Not set') }}</option>
                    @foreach ($locations as $location)
                      <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="mt-3.5 grid grid-cols-1 gap-3.5 sm:grid-cols-3">
                <div>
                  <label class="mb-1.5 block text-xs font-semibold text-muted-soft">{{ __('Acquired on') }}</label>
                  <input type="date" :name="`copies[${index}][acquired_at]`" x-model="copy.acquired_at" class="h-10 w-full rounded-md border border-hairline bg-input px-3 text-sm text-ink" />
                </div>
                <div>
                  <label class="mb-1.5 block text-xs font-semibold text-muted-soft">{{ __('Price paid') }}</label>
                  <input type="number" step="0.01" min="0" placeholder="0.00" :name="`copies[${index}][price_paid]`" x-model="copy.price_paid" class="h-10 w-full rounded-md border border-hairline bg-input px-3 text-sm text-ink placeholder-muted-soft" />
                </div>
                <div>
                  <label class="mb-1.5 block text-xs font-semibold text-muted-soft">{{ __('Est. value') }}</label>
                  <input type="number" step="0.01" min="0" placeholder="0.00" :name="`copies[${index}][estimated_value]`" x-model="copy.estimated_value" class="h-10 w-full rounded-md border border-hairline bg-input px-3 text-sm text-ink placeholder-muted-soft" />
                </div>
              </div>
            </div>
          </template>
        </div>

        <button type="button" x-on:click="addCopy()" class="inline-flex items-center gap-1.5 rounded-md border border-dashed border-hairline px-3.5 py-2 text-[13px] font-semibold text-muted transition-colors hover:text-ink">
          @svg('lucide-plus', 'size-3.5')
          {{ __('Add another copy') }}
        </button>

        <div class="flex items-center justify-end gap-3 pt-2">
          <x-button.secondary href="{{ route('collections.show', $collection) }}" turbo="true">
            {{ __('Cancel') }}
          </x-button.secondary>

          <x-button type="submit" x-bind:disabled="!canSubmit" data-test="add-item-button">
            {{ __('Add item') }}
          </x-button>
        </div>
      </x-form>
    </div>
  </div>
</x-app-layout>
