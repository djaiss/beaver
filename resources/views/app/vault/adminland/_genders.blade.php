<x-box padding="p-0">
  <x-slot:title>{{ __('app/vault.adminland.genders.title') }}</x-slot>

  <x-slot:description>
    <p>{{ __('app/vault.adminland.genders.description') }}</p>
  </x-slot>

  <!-- nb of genders + action -->
  <div id="add-gender-form" class="flex items-center justify-between border-b border-gray-200 p-3 last:border-b-0 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
    @if ($genders->isEmpty())
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.genders.none') }}</p>
    @else
      <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('app/vault.adminland.genders.count', ['count' => $genders->count()]) }}</p>
    @endif

    <x-button.secondary x-target="add-gender-form" href="{{ route('vault.adminland.genders.new', ['vaultId' => $vault->id]) }}" class="mr-2 text-sm">
      {{ __('app/vault.adminland.genders.new') }}
    </x-button.secondary>
  </div>

  <div id="gender-list" class="divide-y divide-gray-200 dark:divide-gray-700" x-data="{
    dragging: null,
    dropTarget: null,
    startDrag(genderId) {
      this.dragging = genderId
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

      this.$refs.reorderForm.action = `/vaults/{{ $vault->id }}/adminland/genders/${this.dragging}/position`
      this.$refs.reorderPosition.value = sameListPosition ?? position
      this.$refs.reorderForm.requestSubmit()
    },
  }">
    <x-form x-ref="reorderForm" x-target="gender-list notifications" method="put" action="" class="hidden">
      <input type="hidden" name="position" x-ref="reorderPosition" />
    </x-form>

    @forelse ($genders as $gender)
      <div id="gender-{{ $gender->id }}" draggable="true" @dragstart="startDrag({{ $gender->id }})" @dragend="clearDrag()" @dragover.prevent="markDropTarget({{ $gender->position }})" @drop.prevent="reorder({{ $gender->position }})" :class="dropTarget === {{ $gender->position }} ? 'bg-blue-50 ring-2 ring-blue-200 dark:bg-gray-800 dark:ring-blue-900/40' : ''" class="group flex items-center justify-between p-3 transition-colors duration-200 hover:bg-blue-50 dark:hover:bg-gray-800">
        <div class="flex items-center gap-2">
          <x-phosphor-dots-six-vertical class="h-4 w-4 cursor-move text-gray-400" />
          <p class="border border-transparent py-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $gender->name }}</p>
        </div>

        <div class="flex gap-2">
          <x-button.invisible x-target="gender-{{ $gender->id }}" href="{{ route('vault.adminland.genders.edit', ['vaultId' => $vault->id, 'gender' => $gender->id]) }}" class="invisible text-sm group-hover:visible">
            {{ __('app/vault.adminland.genders.edit') }}
          </x-button.invisible>

          <x-form
            method="delete"
            x-target="gender-{{ $gender->id }}"
            x-on:ajax:before="
            confirm('{{ __('app/vault.adminland.genders.confirm_delete') }}') ||
              $event.preventDefault()
          "
            action="{{ route('vault.adminland.genders.destroy', ['vaultId' => $vault->id, 'gender' => $gender->id]) }}">
            <x-button.invisible class="invisible text-sm group-hover:visible">
              {{ __('app/shared.delete') }}
            </x-button.invisible>
          </x-form>
        </div>
      </div>
    @empty
      <x-empty-state>
        <x-slot:icon>
          <x-phosphor-gender-intersex class="size-6 text-gray-600" />
        </x-slot>

        {{ __('app/vault.adminland.genders.empty') }}
      </x-empty-state>
    @endforelse
  </div>
</x-box>
