<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DatePrecision;
use App\Enums\TimelineSource;
use App\Enums\TransactionType;
use App\Traits\HasAuthor;
use App\Traits\HasDocuments;
use App\ValueObjects\TimelineEntry;
use Carbon\Carbon;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Transaction
 *
 * A financial or ownership-related exchange involving a copy: a purchase, a
 * sale, a trade, a gift, an inheritance, a refund, a fee, a tax or a shipping
 * charge.
 *
 * Transactions are the single source of truth for commercial data. No other
 * model records an amount that duplicates one, which is why a copy's acquisition
 * date and purchase price are read from here rather than stored on the copy.
 *
 * @property int $id
 * @property int $copy_id
 * @property TransactionType $type
 * @property string|null $counterparty
 * @property int|null $amount
 * @property string|null $currency_code
 * @property int|null $tax_amount
 * @property int|null $fee_amount
 * @property int|null $shipping_amount
 * @property int|null $total_amount
 * @property Carbon $occurred_at
 * @property string|null $reference_number
 * @property string|null $source_url
 * @property string|null $note
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Transaction extends Model
{
    use HasAuthor;
    use HasDocuments;

    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_id',
        'type',
        'counterparty',
        'amount',
        'currency_code',
        'tax_amount',
        'fee_amount',
        'shipping_amount',
        'total_amount',
        'occurred_at',
        'reference_number',
        'source_url',
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
            'type' => TransactionType::class,
            'counterparty' => 'encrypted',
            'amount' => 'integer',
            'tax_amount' => 'integer',
            'fee_amount' => 'integer',
            'shipping_amount' => 'integer',
            'total_amount' => 'integer',
            'occurred_at' => 'date',
            'reference_number' => 'encrypted',
            'source_url' => 'encrypted',
            'note' => 'encrypted',
        ];
    }

    /**
     * Get the copy the exchange involved.
     *
     * @return BelongsTo<Copy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Get the provenance event this exchange produced, if any.
     *
     * Purchases, sales, trades, gifts and inheritances normally have one. Fees,
     * taxes, shipping charges and refunds normally do not: they are the money
     * around a change of ownership rather than the change itself.
     *
     * @return HasOne<ProvenanceEvent, $this>
     */
    public function provenanceEvent(): HasOne
    {
        return $this->hasOne(ProvenanceEvent::class);
    }

    /**
     * What actually changed hands, in cents.
     *
     * The total is optional: someone recording a purchase in a hurry gives the
     * price and nothing else. When it is missing it is the sum of the parts, so
     * a caller never has to decide which of the two to read.
     */
    public function total(): ?int
    {
        if ($this->total_amount !== null) {
            return $this->total_amount;
        }

        $parts = [$this->amount, $this->tax_amount, $this->fee_amount, $this->shipping_amount];

        if (array_filter($parts, fn (?int $part): bool => $part !== null) === []) {
            return null;
        }

        return array_sum($parts);
    }

    /**
     * Map the exchange to a unified-history entry.
     *
     * A purchase, sale, trade, gift or inheritance is a real change of ownership
     * and reads on the default timeline; a fee, tax, shipping charge or refund is
     * the money around one and stays out of it until the complete view is asked
     * for.
     */
    public function toTimelineEntry(): TimelineEntry
    {
        $title = $this->counterparty === null
            ? $this->type->label()
            : $this->type->label().' · '.$this->counterparty;

        return new TimelineEntry(
            source: TimelineSource::Transaction,
            sourceId: $this->id,
            date: $this->occurred_at,
            precision: DatePrecision::Exact,
            title: $title,
            summary: $this->reference_number,
            amountCents: $this->total(),
            currencyCode: $this->currency_code,
            meaningful: $this->type->qualifiesForProvenance(),
        );
    }
}
