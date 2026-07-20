{{--
  The timeline: everything that has happened to the copy, oldest first, drawn
  from the sections that carry real records. Valuations read here, and the
  maintenance marked as part of the object's story does too. The rest of the
  maintenance stays out of the default view and reads in the Activity tab.
--}}

@php
  $entries = collect()
      ->concat($selectedCopy->valuations->map(fn ($valuation): array => [
          'date' => $valuation->valued_at,
          'title' => __('Valued at :amount', ['amount' => $money($valuation->amount)]),
          'sub' => $valuation->type->label(),
          'color' => '#3b82f6',
          'test' => 'history-valuation-'.$valuation->id,
      ]))
      ->concat($selectedCopy->maintenanceRecords
          ->where('include_in_provenance', true)
          ->map(fn ($record): array => [
              'date' => $record->performed_at,
              'title' => $record->title,
              'sub' => $record->type->label(),
              'color' => '#f59e0b',
              'test' => 'history-maintenance-'.$record->id,
          ]))
      ->sortBy(fn (array $entry) => $entry['date']?->timestamp ?? 0)
      ->values();
@endphp

<div class="mb-4">
  <p class="text-lg font-semibold text-ink">{{ __('Timeline') }}</p>
  <p class="mt-1 text-[13px] leading-relaxed text-muted">{{ __('Everything that has happened to this copy, oldest first. The sections listed alongside are what it is assembled from.') }}</p>
</div>

@forelse ($entries as $entry)
  <div class="flex items-start gap-3 border-b border-hairline-soft py-3.5 last:border-b-0" data-test="{{ $entry['test'] }}">
    <span class="mt-1.5 size-2 shrink-0 rounded-sm" style="background-color: {{ $entry['color'] }}"></span>

    <div class="min-w-0 flex-1">
      <p class="text-sm font-semibold text-ink">{{ $entry['title'] }}</p>
      <p class="text-xs text-muted-soft">{{ $entry['sub'] }}</p>
    </div>

    <span class="shrink-0 font-mono text-xs text-muted-soft">{{ $entry['date'] ? $entry['date']->isoFormat('MMM YYYY') : '—' }}</span>
  </div>
@empty
  <div class="rounded-xl border border-hairline">
    <x-empty-state data-test="no-history">
      <x-slot:icon>
        <x-lucide-clock class="size-6 text-muted" />
      </x-slot>

      {{ __('Nothing has been recorded against this copy yet.') }}
    </x-empty-state>
  </div>
@endforelse
