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
              <button type="submit" name="color" value="{{ $swatch }}" aria-label="{{ $swatch }}" class="size-4 cursor-pointer rounded-full ring-offset-1 ring-offset-canvas {{ $type->color === $swatch ? 'ring-2 ring-ink' : '' }}" style="background-color: {{ $swatch }}"></button>
            @endforeach
          </x-form>
        </div>

        <div class="min-w-0 flex-1">
          <div id="name-display">
            <h1 class="truncate text-2xl font-semibold tracking-tight text-ink">{{ $type->name !== '' ? $type->name : __('Untitled type') }}</h1>
          </div>

          <x-form id="name-edit" hidden method="put" :action="route('settings.types.update', $type->id)" data-turbo="true" class="flex items-center gap-2">
            <input type="hidden" name="color" value="{{ $type->color }}" />
            <input id="type-name-input" name="name" value="{{ $type->name }}" placeholder="{{ __('Type name') }}" class="w-full max-w-xs rounded-md border border-hairline bg-input px-3 py-2 text-lg font-semibold text-ink" />
            <x-button type="submit" data-test="save-name-button">{{ __('Save') }}</x-button>
            <button
              type="button"
              onclick="
                document.getElementById('name-edit').hidden = true;
                document.getElementById('name-display').hidden = false;
                document.getElementById('edit-name-button').hidden = false;
              "
              class="cursor-pointer text-sm font-semibold text-muted hover:text-ink">
              {{ __('Cancel') }}
            </button>
          </x-form>

          <p class="mt-2 text-xs text-muted-soft">{{ __('Used by :collections collection(s) · :groups field group(s) · :fields custom field(s)', ['collections' => $type->collections->count(), 'groups' => $type->custom_field_groups_count, 'fields' => $type->custom_fields_count]) }}</p>
        </div>

        <div class="flex shrink-0 items-center gap-3">
          <x-button
            type="button"
            id="edit-name-button"
            data-test="edit-name-button"
            onclick="
              document.getElementById('name-display').hidden = true;
              document.getElementById('name-edit').hidden = false;
              document.getElementById('type-name-input').focus();
              this.hidden = true;
            ">
            {{ __('Edit name') }}
          </x-button>

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
      <h2 class="text-lg font-semibold text-ink">{{ __('Custom fields') }}</h2>
      <p class="mt-0.5 mb-6 max-w-xl text-xs text-muted-soft">{{ __('Organize related fields into groups (e.g. "Grading", "Purchase info"), or add a field directly to the type if it does not belong to a group.') }}</p>

      {{-- Field groups --}}
      <div class="mb-3.5 flex items-center justify-between">
        <h3 class="text-xs font-semibold tracking-wide text-muted-soft uppercase">{{ __('Field groups') }}</h3>

        <x-form method="post" :action="route('settings.types.groups.create', $type->id)" data-turbo="true">
          <button type="submit" data-test="add-group-button" class="cursor-pointer rounded-md border border-dashed border-hairline px-3 py-2 text-xs font-semibold text-ink transition-colors hover:bg-card">+ {{ __('Add group') }}</button>
        </x-form>
      </div>

      <div class="mb-8 flex flex-col gap-4">
        @forelse ($type->customFieldGroups as $group)
          <div class="overflow-hidden rounded-2xl border border-hairline bg-canvas" data-test="group-{{ $group->id }}">
            {{-- Group header: reorder, name, field count, add field, remove. --}}
            <div class="flex items-center gap-2.5 border-b border-hairline bg-card px-4 py-3">
              {{-- Reorder and delete post on their own; their buttons reach them via the form= attribute. --}}
              <x-form method="put" :action="route('settings.types.groups.order.update', [$type->id, $group->id])" id="group-up-{{ $group->id }}" data-turbo="true" class="hidden">
                <input type="hidden" name="direction" value="up" />
              </x-form>
              <x-form method="put" :action="route('settings.types.groups.order.update', [$type->id, $group->id])" id="group-down-{{ $group->id }}" data-turbo="true" class="hidden">
                <input type="hidden" name="direction" value="down" />
              </x-form>
              <x-form method="delete" :action="route('settings.types.groups.destroy', [$type->id, $group->id])" id="group-delete-{{ $group->id }}" data-turbo="true" class="hidden" onsubmit="return confirm('{{ __('Delete this group? Its fields are kept and become standalone fields on the type.') }}')"></x-form>

              <div class="flex shrink-0 flex-col">
                <button type="submit" form="group-up-{{ $group->id }}" aria-label="{{ __('Move group up') }}" data-test="move-group-up-{{ $group->id }}" class="flex h-[15px] w-[26px] cursor-pointer items-center justify-center rounded-t-md border border-hairline bg-canvas text-[9px] text-muted hover:bg-card">▲</button>
                <button type="submit" form="group-down-{{ $group->id }}" aria-label="{{ __('Move group down') }}" data-test="move-group-down-{{ $group->id }}" class="flex h-[15px] w-[26px] cursor-pointer items-center justify-center rounded-b-md border border-t-0 border-hairline bg-canvas text-[9px] text-muted hover:bg-card">▼</button>
              </div>

              <x-form method="put" :action="route('settings.types.groups.update', [$type->id, $group->id])" data-turbo="true" onchange="this.requestSubmit()" class="min-w-0 flex-1">
                <input name="name" value="{{ $group->name }}" placeholder="{{ __('Group name, e.g. Grading') }}" data-test="group-name-{{ $group->id }}" class="w-full rounded-md border border-transparent bg-transparent px-1 py-1.5 text-sm font-semibold text-ink hover:border-hairline" />
              </x-form>

              <span class="shrink-0 text-xs whitespace-nowrap text-muted-soft">{{ trans_choice(':count field|:count fields', $group->customFields->count(), ['count' => $group->customFields->count()]) }}</span>

              <x-form method="post" :action="route('settings.types.groups.fields.create', [$type->id, $group->id])" data-turbo="true" class="shrink-0">
                <button type="submit" data-test="add-field-to-group-{{ $group->id }}" class="cursor-pointer rounded-md border border-dashed border-hairline px-2.5 py-1.5 text-xs font-semibold whitespace-nowrap text-ink transition-colors hover:bg-canvas">+ {{ __('Field') }}</button>
              </x-form>

              <button type="submit" form="group-delete-{{ $group->id }}" aria-label="{{ __('Remove group') }}" data-test="delete-group-{{ $group->id }}" class="flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-md border border-hairline bg-canvas text-muted hover:bg-card">×</button>
            </div>

            <div class="flex flex-col gap-3 p-4">
              @forelse ($group->customFields as $field)
                @include('app.types._field-row', ['type' => $type, 'field' => $field, 'placeholder' => __('e.g. Grading company')])
              @empty
                <div class="rounded-lg border border-dashed border-hairline p-5 text-center text-xs text-muted-soft">{{ __('No fields in this group yet.') }}</div>
              @endforelse
            </div>
          </div>
        @empty
          <div class="rounded-xl border border-dashed border-hairline p-6 text-center text-sm text-muted">{{ __('No field groups yet. Add one to organize related fields together.') }}</div>
        @endforelse
      </div>

      {{-- Standalone fields --}}
      <div class="mb-1 flex items-center justify-between">
        <h3 class="text-xs font-semibold tracking-wide text-muted-soft uppercase">{{ __('Standalone fields') }}</h3>

        <x-form method="post" :action="route('settings.types.fields.create', $type->id)" data-turbo="true">
          <button type="submit" data-test="add-field-button" class="cursor-pointer rounded-md border border-dashed border-hairline px-3 py-2 text-xs font-semibold text-ink transition-colors hover:bg-card">+ {{ __('Add field') }}</button>
        </x-form>
      </div>
      <p class="mb-3.5 text-xs text-muted-soft">{{ __('Fields that live directly on the type, outside of any group.') }}</p>

      <div class="mb-10 flex flex-col gap-3">
        @forelse ($type->ungroupedCustomFields as $field)
          @include('app.types._field-row', ['type' => $type, 'field' => $field, 'placeholder' => __('e.g. Notes')])
        @empty
          <div class="rounded-xl border border-dashed border-hairline p-6 text-center text-sm text-muted">{{ __('No standalone fields. Every field on this type lives in a group.') }}</div>
        @endforelse
      </div>

      {{-- Collections using this type --}}
      <h2 class="text-lg font-semibold text-ink">{{ __('Collections using this type') }}</h2>
      <p class="mt-0.5 mb-3.5 text-xs text-muted-soft">{{ __('A collection can use many types; an item picks exactly one.') }}</p>

      @if ($collections->isEmpty())
        <p class="text-sm text-muted">{{ __('No collections yet. Create one to link it to this type.') }}</p>
      @else
        {{-- Every collection in the account is listed; ticking one links it to the type. --}}
        <x-form method="put" :action="route('settings.types.collections.update', $type->id)" data-turbo="true" onchange="this.requestSubmit()" class="flex flex-wrap gap-2">
          @foreach ($collections as $collection)
            <label class="cursor-pointer">
              <input type="checkbox" name="collection_ids[]" value="{{ $collection->id }}" @checked($type->collections->contains($collection->id)) data-test="collection-chip-{{ $collection->id }}" class="peer sr-only" />
              <span class="flex items-center gap-2 rounded-full border border-hairline px-3.5 py-2 text-sm font-medium text-ink transition-colors hover:bg-card peer-checked:border-ink peer-checked:bg-card">
                <span>{{ $collection->emoji }}</span>
                {{ $collection->name }}
              </span>
            </label>
          @endforeach
        </x-form>
      @endif
    </div>
  </div>
</x-app-layout>
