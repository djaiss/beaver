<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\DatePrecision;
use App\Enums\TimelineSource;
use App\Helpers\ImpreciseDate;
use App\Helpers\Money;
use Carbon\Carbon;

/**
 * One moment in a copy's unified history.
 *
 * This is the shared shape every contributing model maps a row into, so the
 * timeline can merge purchases, valuations, loans and moves without any of them
 * knowing about the others. Nothing here is persisted: an entry is assembled at
 * read time and each source model stays the source of truth for its own data.
 *
 * The date is paired with a precision because provenance is often only known to
 * the year, or not at all. An amount is carried in cents with its own currency
 * so it renders in the currency it was recorded in rather than a converted one.
 */
class TimelineEntry
{
    public function __construct(
        public readonly TimelineSource $source,
        public readonly int $sourceId,
        public readonly ?Carbon $date,
        public readonly DatePrecision $precision,
        public readonly string $title,
        public readonly ?string $summary,
        public readonly ?int $amountCents,
        public readonly ?string $currencyCode,
        public readonly bool $meaningful,
        public readonly ?string $qualifier = null,
    ) {}

    /**
     * A stable identity for the entry, unique across the timeline.
     *
     * The source and the row id place most entries, but a single loan produces
     * both a lending and a return entry from the same row, so a qualifier tells
     * the two apart in the markup and the api.
     */
    public function key(): string
    {
        $key = $this->source->value.'-'.$this->sourceId;

        return $this->qualifier === null ? $key : $key.'-'.$this->qualifier;
    }

    /**
     * The timestamp the timeline sorts on, or null when the entry cannot be
     * placed.
     *
     * An undated event, or one recorded as unknown, has nowhere on the line to
     * sit, so it reads as null and the sort drops it to the end rather than
     * pretending it happened at the epoch.
     */
    public function sortTimestamp(): ?int
    {
        if (! $this->date instanceof Carbon || ! $this->precision->carriesDate()) {
            return null;
        }

        return $this->date->timestamp;
    }

    /**
     * The date in the short form the timeline column shows, honouring precision.
     */
    public function formattedDate(): string
    {
        return ImpreciseDate::short($this->date, $this->precision);
    }

    /**
     * The amount in its own currency, or null when the entry carries no amount.
     */
    public function formattedAmount(): ?string
    {
        if ($this->amountCents === null) {
            return null;
        }

        return Money::format($this->amountCents, $this->currencyCode);
    }
}
