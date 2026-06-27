<x-box padding="p-0">
  <x-slot:title>{{ __('app/vault.adminland.relationship_types.title') }}</x-slot>

  <x-slot:description>
    <p>{{ __('app/vault.adminland.relationship_types.description') }}</p>
  </x-slot>

  <div id="add-relationship-type-category-form" class="flex items-center justify-between border-b border-gray-200 p-3 last:border-b-0 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
    @if ($relationshipTypeCategories->isEmpty())
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.relationship_types.none') }}</p>
    @else
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.relationship_types.count', ['count' => $relationshipTypeCategories->count()]) }}</p>
    @endif

    <x-button.secondary x-target="add-relationship-type-category-form" href="{{ route('vault.adminland.relationship_type_categories.new', ['vaultId' => $vault->id]) }}" class="mr-2 text-sm">
      {{ __('app/vault.adminland.relationship_types.new_category') }}
    </x-button.secondary>
  </div>

  <div id="relationship-type-category-list" class="divide-y divide-gray-200 dark:divide-gray-700">
    @forelse ($relationshipTypeCategories as $relationshipTypeCategory)
      <section id="relationship-type-category-{{ $relationshipTypeCategory->id }}">
        <div class="group/category flex items-center justify-between gap-3 border-b border-gray-200 bg-gray-100 p-3 transition-colors duration-200 hover:bg-blue-50 dark:border-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800">
          <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $relationshipTypeCategory->name }}</p>

          <div class="flex flex-wrap justify-end gap-2">
            <x-button.invisible x-target="add-relationship-type-{{ $relationshipTypeCategory->id }}-form" href="{{ route('vault.adminland.relationship_types.new', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id]) }}" class="invisible text-sm group-hover/category:visible">
              {{ __('app/vault.adminland.relationship_types.new_type') }}
            </x-button.invisible>

            <x-button.invisible x-target="relationship-type-category-{{ $relationshipTypeCategory->id }}" href="{{ route('vault.adminland.relationship_type_categories.edit', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id]) }}" class="invisible text-sm group-hover/category:visible">
              {{ __('app/vault.adminland.relationship_types.edit_category') }}
            </x-button.invisible>

            @if ($relationshipTypeCategory->can_be_deleted)
              <x-form
                method="delete"
                x-target="relationship-type-category-{{ $relationshipTypeCategory->id }} notifications"
                x-on:ajax:before="
                  confirm('{{ __('app/vault.adminland.relationship_types.confirm_delete_category') }}') ||
                    $event.preventDefault()
                "
                action="{{ route('vault.adminland.relationship_type_categories.destroy', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id]) }}">
                <x-button.invisible class="invisible text-sm group-hover/category:visible">
                  {{ __('app/shared.delete') }}
                </x-button.invisible>
              </x-form>
            @endif
          </div>
        </div>

        <div id="add-relationship-type-{{ $relationshipTypeCategory->id }}-form"></div>

        <div id="relationship-type-list-{{ $relationshipTypeCategory->id }}" x-data="relationshipTypeSorter({{ $vault->id }}, {{ $relationshipTypeCategory->id }})" class="divide-y divide-gray-200 dark:divide-gray-700">
          <x-form x-ref="reorderForm" x-target="relationship-type-list-{{ $relationshipTypeCategory->id }} notifications" method="put" action="" class="hidden">
            <input type="hidden" name="position" x-ref="reorderPosition" />
          </x-form>

          @forelse ($relationshipTypeCategory->relationshipTypes as $relationshipType)
            <div id="relationship-type-{{ $relationshipType->id }}" draggable="true" @dragstart="startDrag({{ $relationshipType->id }})" @dragend="clearDrag()" @dragover.prevent="markDropTarget({{ $relationshipType->position }})" @drop.prevent="reorder({{ $relationshipType->position }})" :class="dropTarget === {{ $relationshipType->position }} ? 'bg-blue-50 ring-2 ring-inset ring-blue-200 dark:bg-gray-800 dark:ring-blue-900/40' : ''" class="group/type flex items-center justify-between gap-3 p-3 transition-colors duration-200 hover:bg-blue-50 dark:hover:bg-gray-800">
              <div class="flex min-w-0 items-center gap-2">
                <x-phosphor-dots-six-vertical class="h-4 w-4 shrink-0 cursor-move text-gray-400" />
                <p class="truncate text-sm text-gray-700 dark:text-gray-300">{{ $relationshipType->name }}</p>
              </div>

              <div class="flex shrink-0 gap-2">
                <x-button.invisible x-target="relationship-type-{{ $relationshipType->id }}" href="{{ route('vault.adminland.relationship_types.edit', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id, 'relationshipType' => $relationshipType->id]) }}" class="invisible text-sm group-hover/type:visible">
                  {{ __('app/vault.adminland.relationship_types.edit_type') }}
                </x-button.invisible>

                @if ($relationshipType->can_be_deleted)
                  <x-form
                    method="delete"
                    x-target="relationship-type-{{ $relationshipType->id }} notifications"
                    x-on:ajax:before="
                      confirm('{{ __('app/vault.adminland.relationship_types.confirm_delete_type') }}') ||
                        $event.preventDefault()
                    "
                    action="{{ route('vault.adminland.relationship_types.destroy', ['vaultId' => $vault->id, 'relationshipTypeCategory' => $relationshipTypeCategory->id, 'relationshipType' => $relationshipType->id]) }}">
                    <x-button.invisible class="invisible text-sm group-hover/type:visible">
                      {{ __('app/shared.delete') }}
                    </x-button.invisible>
                  </x-form>
                @endif
              </div>
            </div>
          @empty
            <p class="px-7 py-4 text-sm text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.relationship_types.empty_category') }}</p>
          @endforelse
        </div>
      </section>
    @empty
      <x-empty-state>
        <x-slot:icon>
          <x-phosphor-users-three class="size-6 text-gray-600" />
        </x-slot>

        {{ __('app/vault.adminland.relationship_types.empty') }}
      </x-empty-state>
    @endforelse
  </div>
</x-box>
