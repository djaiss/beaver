<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DatePrecision;
use App\Enums\InsuranceStatus;
use App\Enums\TimelineSource;
use App\Traits\HasAuthor;
use App\Traits\HasDocuments;
use App\ValueObjects\TimelineEntry;
use Carbon\Carbon;
use Database\Factories\InsuranceRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InsuranceRecord
 *
 * A piece of insurance coverage for a copy: an insurer, an insured value and the
 * window it holds for. A copy gathers several of these over its life as policies,
 * providers and insured values change, so the model is historical rather than a
 * single current-coverage field, and changing the insured value records a new row
 * rather than overwriting an old one.
 *
 * @property int $id
 * @property int $copy_id
 * @property string $provider
 * @property string|null $policy_number
 * @property string|null $coverage_type
 * @property int $insured_value
 * @property string|null $currency_code
 * @property int|null $deductible_amount
 * @property string|null $deductible_currency_code
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property InsuranceStatus $status
 * @property bool $is_scheduled_item
 * @property string|null $contact_name
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $note
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class InsuranceRecord extends Model
{
    use HasAuthor;
    use HasDocuments;

    /** @use HasFactory<InsuranceRecordFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_id',
        'provider',
        'policy_number',
        'coverage_type',
        'insured_value',
        'currency_code',
        'deductible_amount',
        'deductible_currency_code',
        'starts_at',
        'ends_at',
        'status',
        'is_scheduled_item',
        'contact_name',
        'contact_email',
        'contact_phone',
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
            'provider' => 'encrypted',
            'policy_number' => 'encrypted',
            'coverage_type' => 'encrypted',
            'insured_value' => 'integer',
            'deductible_amount' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'status' => InsuranceStatus::class,
            'is_scheduled_item' => 'boolean',
            'contact_name' => 'encrypted',
            'contact_email' => 'encrypted',
            'contact_phone' => 'encrypted',
            'note' => 'encrypted',
        ];
    }

    /**
     * Get the copy this covers.
     *
     * @return BelongsTo<Copy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Map the coverage to a unified-history entry.
     *
     * Coverage opening is a meaningful moment in a copy's care, so it reads on
     * the default timeline at the date the policy started, with the insured value
     * in its own currency.
     */
    public function toTimelineEntry(): TimelineEntry
    {
        $title = $this->policy_number === null
            ? $this->provider
            : $this->provider.' · '.$this->policy_number;

        return new TimelineEntry(
            source: TimelineSource::Insurance,
            sourceId: $this->id,
            date: $this->starts_at,
            precision: DatePrecision::Exact,
            title: $title,
            summary: $this->coverage_type,
            amountCents: $this->insured_value,
            currencyCode: $this->currency_code,
            meaningful: true,
        );
    }
}
