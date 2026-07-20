<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Models\Concerns\HasAuthor;
use App\Models\Concerns\HasDocuments;
use Carbon\Carbon;
use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Loan
 *
 * The temporary transfer of custody of a copy without any transfer of ownership.
 * It covers both directions: a piece lent out to a friend, a gallery or a museum
 * (outgoing), and a piece borrowed in (incoming).
 *
 * A loan records the party, an optional purpose, the dates and a status, and,
 * because a loaned object leaves and comes back, the condition on the way out and
 * on the way back so damage in transit is visible. A deposit with its own
 * currency covers the institutional loans that require one.
 *
 * An active outgoing loan means the object is not currently in the account's
 * physical custody. When it is significant enough, the loan and its return also
 * generate matching provenance events so an exhibition or an institutional loan
 * joins the object's documented story rather than staying loan history.
 *
 * @property int $id
 * @property int $copy_id
 * @property int|null $loan_provenance_event_id
 * @property int|null $return_provenance_event_id
 * @property LoanDirection $direction
 * @property LoanStatus $status
 * @property string $party
 * @property string|null $purpose
 * @property Carbon $loaned_at
 * @property Carbon|null $due_at
 * @property Carbon|null $returned_at
 * @property int|null $condition_out_id
 * @property int|null $condition_in_id
 * @property int|null $deposit_amount
 * @property string|null $deposit_currency_code
 * @property bool $include_in_provenance
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Loan extends Model
{
    use HasAuthor;
    use HasDocuments;

    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'copy_id',
        'loan_provenance_event_id',
        'return_provenance_event_id',
        'direction',
        'status',
        'party',
        'purpose',
        'loaned_at',
        'due_at',
        'returned_at',
        'condition_out_id',
        'condition_in_id',
        'deposit_amount',
        'deposit_currency_code',
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
            'direction' => LoanDirection::class,
            'status' => LoanStatus::class,
            'party' => 'encrypted',
            'purpose' => 'encrypted',
            'loaned_at' => 'date',
            'due_at' => 'date',
            'returned_at' => 'date',
            'deposit_amount' => 'integer',
            'include_in_provenance' => 'boolean',
        ];
    }

    /**
     * Get the copy whose custody moved.
     *
     * @return BelongsTo<Copy, $this>
     */
    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }

    /**
     * Get the copy's condition when it left, if recorded.
     *
     * @return BelongsTo<Condition, $this>
     */
    public function conditionOut(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'condition_out_id');
    }

    /**
     * Get the copy's condition when it came back, if recorded.
     *
     * @return BelongsTo<Condition, $this>
     */
    public function conditionIn(): BelongsTo
    {
        return $this->belongsTo(Condition::class, 'condition_in_id');
    }

    /**
     * Get the provenance event the loan generated, if any.
     *
     * @return BelongsTo<ProvenanceEvent, $this>
     */
    public function loanProvenanceEvent(): BelongsTo
    {
        return $this->belongsTo(ProvenanceEvent::class, 'loan_provenance_event_id');
    }

    /**
     * Get the provenance event the return generated, if any.
     *
     * @return BelongsTo<ProvenanceEvent, $this>
     */
    public function returnProvenanceEvent(): BelongsTo
    {
        return $this->belongsTo(ProvenanceEvent::class, 'return_provenance_event_id');
    }

    /**
     * Whether the object is out under this loan.
     *
     * An outgoing loan that is active or overdue has the object in someone else's
     * hands, which is what the copy's loaned-out state mirrors. An incoming loan
     * is the account holding someone else's object, so it never reads this way.
     */
    public function isOutstanding(): bool
    {
        return $this->direction === LoanDirection::Outgoing
            && $this->status->hasLeftCustody();
    }

    /**
     * Whether the loan is outgoing and past its due date without a return.
     */
    public function isOverdue(): bool
    {
        return $this->status === LoanStatus::Overdue;
    }
}
