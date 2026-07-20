<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MaintenanceType;
use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\MaintenanceRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class MaintenanceRecord
 *
 * A piece of work performed on a copy: a cleaning, a repair, a servicing, a
 * conservation, a restoration, a replacement or an inspection. It records who did
 * it, when, what it cost, and the condition of the copy before and after, so the
 * work reads as a real before-and-after rather than a note. A record can also
 * carry a next-due date for recurring care, and, when significant, generate a
 * matching provenance event so the work joins the object's documented story.
 *
 * @property int $id
 * @property int $copy_id
 * @property int|null $provenance_event_id
 * @property MaintenanceType $type
 * @property string $title
 * @property string|null $description
 * @property string|null $performed_by
 * @property Carbon|null $performed_at
 * @property int|null $cost_amount
 * @property string|null $cost_currency_code
 * @property int|null $condition_before_id
 * @property int|null $condition_after_id
 * @property Carbon|null $next_due_at
 * @property bool $include_in_provenance
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class MaintenanceRecord extends Model
{
    use HasAuthor;

    /** @use HasFactory<MaintenanceRecordFactory> */
    use HasFactory;

    /**
     * The number of days before its due date a record starts reading as due soon.
     */
    private const int DUE_SOON_DAYS = 30;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_id',
        'provenance_event_id',
        'type',
        'title',
        'description',
        'performed_by',
        'performed_at',
        'cost_amount',
        'cost_currency_code',
        'condition_before_id',
        'condition_after_id',
        'next_due_at',
        'include_in_provenance',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => MaintenanceType::class,
            'title' => 'encrypted',
            'description' => 'encrypted',
            'performed_by' => 'encrypted',
            'performed_at' => 'date',
            'cost_amount' => 'integer',
            'next_due_at' => 'date',
            'include_in_provenance' => 'boolean',
        ];
    }

    /**
     * Get the copy the work was performed on.
     *
     * @return BelongsTo<Copy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Get the copy's condition before the work, if recorded.
     *
     * @return BelongsTo<Condition, $this>
     */
    public function conditionBefore(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'condition_before_id');
    }

    /**
     * Get the copy's condition after the work, if recorded.
     *
     * @return BelongsTo<Condition, $this>
     */
    public function conditionAfter(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'condition_after_id');
    }

    /**
     * Get the provenance event this record generated, if any.
     *
     * @return BelongsTo<ProvenanceEvent, $this>
     */
    public function provenanceEvent(): BelongsTo
    {
        return $this->belongsTo(ProvenanceEvent::class);
    }

    /**
     * Whether the next care is due soon or already overdue.
     *
     * A record with no next-due date is never due, so it reads false rather than
     * being coerced into a date.
     */
    public function isDueSoon(): bool
    {
        if ($this->next_due_at === null) {
            return false;
        }

        return $this->next_due_at->lessThanOrEqualTo(now()->addDays(self::DUE_SOON_DAYS));
    }
}
