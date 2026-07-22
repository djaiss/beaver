<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TimelineSource;
use App\Models\Copy;
use App\ValueObjects\TimelineEntry;
use Illuminate\Support\Collection;

/**
 * Assembles a copy's unified history from the models hanging off it.
 *
 * The timeline is a read model, not a table. Nothing is copied into a history
 * store: each contributing model maps its own rows to a shared entry shape, this
 * merges them, and each model stays the source of truth for its own data. A
 * dedicated table would only be justified if this proved too slow, which it will
 * not at the scale of one copy.
 *
 * The contributing relations are eager loaded up front, so the whole timeline is
 * a bounded number of queries however many entries it holds. Documents and
 * related copies are deliberately not on the timeline: a document is evidence
 * for another entry rather than an event of its own, and a related copy is a link
 * between objects rather than a moment in this one's life.
 */
class BuildCopyHistory
{
    public function __construct(
        private readonly Copy $copy,
    ) {
        $this->copy->loadMissing([
            'transactions',
            'provenanceEvents',
            'valuations',
            'insuranceRecords',
            'maintenanceRecords',
            'loans.itemConditionIn',
            'locationHistory.location',
        ]);
    }

    /**
     * The timeline, newest first.
     *
     * By default only the historically meaningful entries read: routine
     * maintenance, ordinary moves and informal loans stay out until the complete
     * view is asked for. A type filter narrows the result to chosen sources, and
     * is applied after the meaningful filter so the two compose.
     *
     * @param  list<string>  $types  The `TimelineSource` values to keep, or empty for all.
     * @return list<TimelineEntry>
     */
    public function entries(bool $meaningfulOnly = true, array $types = []): array
    {
        $entries = $this->allEntries();

        if ($meaningfulOnly) {
            $entries = $entries->filter(fn (TimelineEntry $entry): bool => $entry->meaningful);
        }

        if ($types !== []) {
            $entries = $entries->filter(fn (TimelineEntry $entry): bool => in_array($entry->source->value, $types, true));
        }

        return $entries
            ->sort($this->newestFirst(...))
            ->values()
            ->all();
    }

    /**
     * The sources that contribute at least one entry, in the order they first
     * appear on the timeline.
     *
     * The type filter only offers the sources a copy actually has, so a copy
     * that was never insured shows no insurance chip. This looks across every
     * entry, meaningful or not, so a source that only appears in the complete
     * view is still offered. The chips follow the newest-first order of the
     * timeline, so the source of the most recent event reads first.
     *
     * @return list<TimelineSource>
     */
    public function presentSources(): array
    {
        $present = [];

        foreach ($this->allEntries()->sort($this->newestFirst(...)) as $entry) {
            $present[$entry->source->value] = $entry->source;
        }

        return array_values($present);
    }

    /**
     * Every entry the copy carries, mapped from each contributing model.
     *
     * @return Collection<int, TimelineEntry>
     */
    private function allEntries(): Collection
    {
        return collect()
            ->concat($this->copy->transactions->map(fn ($transaction): TimelineEntry => $transaction->toTimelineEntry()))
            ->concat($this->copy->provenanceEvents->map(fn ($event): TimelineEntry => $event->toTimelineEntry()))
            ->concat($this->copy->valuations->map(fn ($valuation): TimelineEntry => $valuation->toTimelineEntry()))
            ->concat($this->copy->insuranceRecords->map(fn ($record): TimelineEntry => $record->toTimelineEntry()))
            ->concat($this->copy->maintenanceRecords->map(fn ($record): TimelineEntry => $record->toTimelineEntry()))
            ->concat($this->copy->loans->flatMap(fn ($loan): array => $loan->toTimelineEntries()))
            ->concat($this->copy->locationHistory->map(fn ($move): TimelineEntry => $move->toTimelineEntry()));
    }

    /**
     * Order two entries newest first, dropping the undated ones to the end.
     *
     * An entry with no placeable date has no timestamp, so it cannot claim a spot
     * on the line and sits after everything that can. Two entries that share a
     * date keep the order they were assembled in, since the sort is stable.
     */
    private function newestFirst(TimelineEntry $a, TimelineEntry $b): int
    {
        $first = $a->sortTimestamp();
        $second = $b->sortTimestamp();

        if ($first === $second) {
            return 0;
        }

        if ($first === null) {
            return 1;
        }

        if ($second === null) {
            return -1;
        }

        return $second <=> $first;
    }
}
