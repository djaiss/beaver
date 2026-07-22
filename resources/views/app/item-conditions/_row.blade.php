@php
  $searchable = mb_strtolower($condition->name);
@endphp

<div
  x-data="{
    editing: false,
    confirmMessage: @js(__('Delete this condition? Copies currently set to this condition will lose it. This cannot be undone.')),
    edit() {
      this.editing = true
      this.$nextTick(() => {
        this.$refs.input.focus()
        this.$refs.input.select()
      })
    },
    save() {
      if (! this.editing) {
        return
      }

      this.editing = false
      this.$refs.form.requestSubmit()
    },
    cancel() {
      this.editing = false
      this.$refs.input.value = @js($condition->name)
    },
  }"
  x-show="matches($el.dataset.conditionName)"
  data-condition-name="{{ $searchable }}"
  data-test="condition-row-{{ $condition->id }}"
  class="flex items-center gap-3 border-b border-hairline-soft px-5 py-3 last:border-b-0"
>
  @svg('lucide-gauge', 'size-4 shrink-0 text-muted-soft')

  <x-form
    method="put"
    :action="route('settings.itemConditions.update', $condition->id)"
    x-ref="form"
    x-target="conditions-search conditions-list notifications"
    class="min-w-0 flex-1"
  >
    <input
      name="name"
      value="{{ $condition->name }}"
      maxlength="255"
      required
      x-ref="input"
      :readonly="! editing"
      :class="editing ? 'border-hairline bg-input' : 'cursor-default border-transparent bg-transparent'"
      x-on:blur="save()"
      x-on:keydown.enter.prevent="save()"
      x-on:keydown.escape.prevent="cancel()"
      class="w-full truncate rounded-md border px-2 py-1 text-sm font-semibold text-ink focus:outline-none"
      data-test="condition-name-{{ $condition->id }}"
    />
  </x-form>

  <span class="w-28 shrink-0 text-right text-xs text-muted-soft">{{ $condition->updated_at?->diffForHumans() }}</span>

  <div class="flex w-[72px] shrink-0 items-center justify-end gap-1.5">
    <button
      type="button"
      x-on:click="edit()"
      class="flex size-8 items-center justify-center rounded-md border border-hairline text-muted hover:bg-card"
      aria-label="{{ __('Rename condition') }}"
      title="{{ __('Rename') }}"
      data-test="edit-condition-{{ $condition->id }}"
    >
      @svg('lucide-pencil', 'size-3.5')
    </button>

    <x-form
      method="delete"
      :action="route('settings.itemConditions.destroy', $condition->id)"
      x-target="conditions-search conditions-list notifications"
      x-on:ajax:before="confirm(confirmMessage) || $event.preventDefault()"
    >
      <button
        type="submit"
        class="flex size-8 items-center justify-center rounded-md border border-hairline text-muted hover:bg-card"
        aria-label="{{ __('Delete condition') }}"
        title="{{ __('Delete') }}"
        data-test="delete-condition-{{ $condition->id }}"
      >
        @svg('lucide-x', 'size-3.5')
      </button>
    </x-form>
  </div>
</div>
