<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DatePrecision;
use App\Enums\TimelineSource;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Models\Concerns\HasAuthor;
use App\Models\Concerns\HasDocuments;
use App\ValueObjects\TimelineEntry;
use Carbon\Carbon;
use Database\Factories\ValuationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Valuation
 *
 * What a copy was reckoned to be worth at one point in time. Replaces the single
 * mutable estimated value the copy used to carry.
 *
 * Valuations are append-only: changing what a copy is worth records a new row
 * rather than overwriting an old one, and the latest by valued_at is what the
 * application shows as the current estimated value. A purchase price is not a
 * valuation, it belongs to a transaction.
 *
 * @property int $id
 * @property int $copy_id
 * @property ValuationType $type
 * @property int $amount
 * @property string|null $currency_code
 * @property Carbon $valued_at
 * @property string|null $valuer
 * @property string|null $method
 * @property ValuationConfidence $confidence
 * @property string|null $source_url
 * @property string|null $reference_number
 * @property string|null $note
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Valuation extends Model
{
    use HasAuthor;
    use HasDocuments;

    /** @use HasFactory<ValuationFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_id',
        'type',
        'amount',
        'currency_code',
        'valued_at',
        'valuer',
        'method',
        'confidence',
        'source_url',
        'reference_number',
        'note',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ValuationType::class,
            'amount' => 'integer',
            'valued_at' => 'date',
            'valuer' => 'encrypted',
            'method' => 'encrypted',
            'confidence' => ValuationConfidence::class,
            'source_url' => 'encrypted',
            'reference_number' => 'encrypted',
            'note' => 'encrypted',
        ];
    }

    /**
     * Get the copy this values.
     *
     * @return BelongsTo<Copy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Map the valuation to a unified-history entry.
     *
     * A valuation is always part of what an object is worth over time, so it
     * reads on the default timeline, and the amount renders in the currency it
     * was recorded in.
     */
    public function toTimelineEntry(): TimelineEntry
    {
        return new TimelineEntry(
            source: TimelineSource::Valuation,
            sourceId: $this->id,
            date: $this->valued_at,
            precision: DatePrecision::Exact,
            title: $this->type->label(),
            summary: $this->valuer,
            amountCents: $this->amount,
            currencyCode: $this->currency_code,
            meaningful: true,
        );
    }
}
