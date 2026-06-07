<x-box padding="p-0">
  <x-slot:title>{{ __('app/vault.adminland.marital-statuses.title') }}</x-slot>

  <x-slot:description>
    <p>{{ __('app/vault.adminland.marital-statuses.description') }}</p>
  </x-slot>

  <!-- nb of marital statuses + action -->
  <div id="add-marital-status-form" class="flex items-center justify-between border-b border-gray-200 p-3 last:border-b-0 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
    @if ($maritalStatuses->isEmpty())
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.marital-statuses.none') }}</p>
    @else
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.marital-statuses.count', ['count' => $maritalStatuses->count()]) }}</p>
    @endif

    <x-button.secondary x-target="add-marital-status-form" href="{{ route('vault.adminland.marital-statuses.new', ['vaultId' => $vault->id]) }}" class="mr-2 text-sm">
      {{ __('app/vault.adminland.marital-statuses.new') }}
    </x-button.secondary>
  </div>

  <div id="marital-status-list" class="divide-y divide-gray-200 dark:divide-gray-700" x-data="{
    dragging: null,
    dropTarget: null,
    startDrag(maritalStatusId) {
      this.dragging = maritalStatusId
    },
    clearDrag() {
      this.dragging = null
      this.dropTarget = null
    },
    markDropTarget(position) {
      this.dropTarget = position
    },
    reorder(position, sameListPosition = null) {
      if (! this.dragging) {
        return
      }

      this.$refs.reorderForm.action = `/vaults/{{ $vault->id }}/adminland/marital-statuses/${this.dragging}/position`
      this.$refs.reorderPosition.value = sameListPosition ?? position
      this.$refs.reorderForm.requestSubmit()
    },
  }">
    <x-form x-ref="reorderForm" x-target="marital-status-list notifications" method="put" action="" class="hidden">
      <input type="hidden" name="position" x-ref="reorderPosition" />
    </x-form>

    @forelse ($maritalStatuses as $maritalStatus)
      <div id="marital-status-{{ $maritalStatus->id }}" draggable="true" @dragstart="startDrag({{ $maritalStatus->id }})" @dragend="clearDrag()" @dragover.prevent="markDropTarget({{ $maritalStatus->position }})" @drop.prevent="reorder({{ $maritalStatus->position }})" :class="dropTarget === {{ $maritalStatus->position }} ? 'bg-blue-50 ring-2 ring-blue-200 dark:bg-gray-800 dark:ring-blue-900/40' : ''" class="group flex items-center justify-between p-3 transition-colors duration-200 hover:bg-blue-50 dark:hover:bg-gray-800">
        <div class="flex items-center gap-2">
          <x-phosphor-dots-six-vertical class="h-4 w-4 cursor-move text-gray-400" />
          <p class="border border-transparent py-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $maritalStatus->name }}</p>
        </div>

        <div class="flex gap-2">
          <x-button.invisible x-target="marital-status-{{ $maritalStatus->id }}" href="{{ route('vault.adminland.marital-statuses.edit', ['vaultId' => $vault->id, 'maritalStatus' => $maritalStatus->id]) }}" class="invisible text-sm group-hover:visible">
            {{ __('app/vault.adminland.marital-statuses.edit') }}
          </x-button.invisible>

          <x-form
            method="delete"
            x-target="marital-status-{{ $maritalStatus->id }}"
            x-on:ajax:before="
            confirm('{{ __('app/vault.adminland.marital-statuses.confirm_delete') }}') ||
              $event.preventDefault()
          "
            action="{{ route('vault.adminland.marital-statuses.destroy', ['vaultId' => $vault->id, 'maritalStatus' => $maritalStatus->id]) }}">
            <x-button.invisible class="invisible text-sm group-hover:visible">
              {{ __('app/shared.delete') }}
            </x-button.invisible>
          </x-form>
        </div>
      </div>
    @empty
      <x-empty-state>
        <x-slot:icon>
          <x-phosphor-heart class="size-6 text-gray-600" />
        </x-slot>

        {{ __('app/vault.adminland.marital-statuses.empty') }}
      </x-empty-state>
    @endforelse
  </div>
</x-box>
