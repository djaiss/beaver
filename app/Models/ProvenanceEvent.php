<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DatePrecision;
use App\Enums\ProvenanceEventType;
use App\Helpers\ImpreciseDate;
use App\Models\Concerns\HasAuthor;
use App\Models\Concerns\HasDocuments;
use Carbon\Carbon;
use Database\Factories\ProvenanceEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProvenanceEvent
 *
 * A meaningful moment in the ownership, custody, origin, authenticity or
 * documented historical significance of a copy. This is the collector-facing
 * story of the object: acquired from an auction house in 1987, authenticated by
 * a named specialist, loaned to a museum for an exhibition.
 *
 * No amounts live here. Financial data belongs to Transaction and must not be
 * duplicated, so an event that came from an exchange links to the transaction
 * rather than restating what was paid.
 *
 * Provenance dates are frequently uncertain, so the date is paired with how much
 * of it is actually known and is rendered against that.
 *
 * @property int $id
 * @property int $copy_id
 * @property int|null $transaction_id
 * @property ProvenanceEventType $type
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $occurred_at
 * @property DatePrecision $occurred_at_precision
 * @property string|null $location
 * @property string|null $from_party
 * @property string|null $to_party
 * @property string|null $reference_number
 * @property string|null $source_url
 * @property bool $is_verified
 * @property string|null $verification_note
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class ProvenanceEvent extends Model
{
    use HasAuthor;
    use HasDocuments;

    /** @use HasFactory<ProvenanceEventFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_id',
        'transaction_id',
        'type',
        'title',
        'description',
        'occurred_at',
        'occurred_at_precision',
        'location',
        'from_party',
        'to_party',
        'reference_number',
        'source_url',
        'is_verified',
        'verification_note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ProvenanceEventType::class,
            'title' => 'encrypted',
            'description' => 'encrypted',
            'occurred_at' => 'date',
            'occurred_at_precision' => DatePrecision::class,
            'location' => 'encrypted',
            'from_party' => 'encrypted',
            'to_party' => 'encrypted',
            'reference_number' => 'encrypted',
            'source_url' => 'encrypted',
            'is_verified' => 'boolean',
            'verification_note' => 'encrypted',
        ];
    }

    /**
     * Get the copy whose story this is part of.
     *
     * @return BelongsTo<Copy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Get the transaction this event came from, if any.
     *
     * An event can exist with no transaction, and a transaction with no event.
     * Deleting the transaction unlinks this rather than deleting it.
     *
     * @return BelongsTo<Transaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the date, rendered at the precision it was recorded at.
     */
    public function formattedDate(): string
    {
        return ImpreciseDate::format($this->occurred_at, $this->occurred_at_precision);
    }

    /**
     * Get the date in the short form the timeline column uses.
     */
    public function shortDate(): string
    {
        return ImpreciseDate::short($this->occurred_at, $this->occurred_at_precision);
    }
}
