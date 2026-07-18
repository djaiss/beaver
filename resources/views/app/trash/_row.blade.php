@php
  $type = $entry['type'];
  $searchable = mb_strtolower($entry['name'].' '.$entry['subtitle']);

  // The closer a row is to being purged, the louder its countdown.
  $urgency = match (true) {
      $entry['days_left'] <= 3 => 'text-error',
      $entry['days_left'] <= 7 => 'text-warning',
      default => 'text-muted',
  };
@endphp

<div
  x-show="matches($el.dataset.trashType, $el.dataset.trashName)"
  data-trash-row
  data-trash-type="{{ $type->value }}"
  data-trash-name="{{ $searchable }}"
  data-test="trash-row-{{ $type->value }}-{{ $entry['id'] }}"
  class="flex items-center gap-4 border-b border-hairline-soft px-1 py-3.5 last:border-b-0"
>
  <div class="flex min-w-0 flex-1 items-center gap-3">
    <span class="flex size-8.5 shrink-0 items-center justify-center rounded-[9px] {{ $type->badgeClasses() }}">
      @svg('lucide-'.$type->icon(), 'size-4')
    </span>

    <div class="min-w-0">
      <p class="truncate text-sm font-semibold text-ink">{{ $entry['name'] }}</p>
      <p class="truncate text-xs text-muted-soft">{{ $entry['subtitle'] }}</p>
    </div>
  </div>

  <div class="w-28 shrink-0">
    <span class="rounded-md px-2 py-0.5 text-xs font-semibold {{ $type->badgeClasses() }}">{{ $type->label() }}</span>
  </div>

  <div class="w-36 shrink-0">
    <p class="text-[13px] text-ink">{{ $entry['deleted_at']->diffForHumans() }}</p>
    @if ($entry['deleted_by_name'])
      <p class="truncate text-xs text-muted-soft">{{ __('by :name', ['name' => $entry['deleted_by_name']]) }}</p>
    @endif
  </div>

  <div class="flex w-28 shrink-0 items-center gap-2">
    <span class="size-1.5 shrink-0 rounded-full bg-current {{ $urgency }}"></span>
    <span class="text-[13px] font-semibold {{ $urgency }}">{{ trans_choice(':count day left|:count days left', $entry['days_left'], ['count' => $entry['days_left']]) }}</span>
  </div>

  <div class="flex w-24 shrink-0 items-center justify-end">
    <x-form
      method="put"
      :action="route('settings.trash.update')"
      x-target="trash-list notifications"
    >
      <input type="hidden" name="type" value="{{ $type->value }}" />
      <input type="hidden" name="id" value="{{ $entry['id'] }}" />

      <button
        type="submit"
        class="flex h-8 cursor-pointer items-center gap-1.5 rounded-md border border-hairline px-3 text-[13px] font-semibold text-ink hover:bg-card"
        data-test="restore-{{ $type->value }}-{{ $entry['id'] }}"
      >
        @svg('lucide-rotate-ccw', 'size-3.5')
        {{ __('Restore') }}
      </button>
    </x-form>
  </div>
</div>
