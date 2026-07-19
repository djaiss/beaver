<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CopyStatus;
use App\Enums\TransactionType;
use App\Models\Concerns\HasAuthor;
use App\Models\Concerns\HasDeleter;
use Carbon\Carbon;
use Database\Factories\CopyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Copy
 *
 * A single physical instance of an item. An item owned three times has three
 * copies, each with its own condition, location and history.
 *
 * A copy is a current-state record. Everything historical about it lives in its
 * own model: what it has been worth in Valuation, and in time what was paid for
 * it and where it has been. The condition and the current location stay here as
 * denormalized pointers to the current state, so a list screen can render a row
 * without joining the history tables.
 *
 * @property int $id
 * @property int $item_id
 * @property string|null $identifier
 * @property int|null $condition_id
 * @property int|null $current_location_id
 * @property CopyStatus $status
 * @property int $quantity
 * @property Carbon|null $disposed_at
 * @property string|null $note
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property int|null $deleted_by_id
 * @property string|null $deleted_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Copy extends Model
{
    use HasAuthor;
    use HasDeleter;

    /** @use HasFactory<CopyFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'copies';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_id',
        'identifier',
        'condition_id',
        'current_location_id',
        'status',
        'quantity',
        'disposed_at',
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
            'identifier' => 'encrypted',
            'status' => CopyStatus::class,
            'quantity' => 'integer',
            'disposed_at' => 'date',
            'note' => 'encrypted',
        ];
    }

    /**
     * Get the item this is a copy of.
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the current condition of the copy, if any.
     *
     * @return BelongsTo<Condition, $this>
     */
    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * Get the location where the copy is currently stored, if any.
     *
     * @return BelongsTo<Location, $this>
     */
    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    /**
     * Get every transaction involving the copy, most recent first.
     *
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->orderByDesc('occurred_at')->orderByDesc('id');
    }

    /**
     * Get the transaction that brought the copy into the collection.
     *
     * The acquisition date and the purchase price are read from here rather than
     * stored on the copy. It is the earliest transaction of a type that acquires
     * (a purchase, a trade, a gift received, an inheritance), so a copy bought
     * and later sold still reports when it was bought.
     *
     * @return HasOne<Transaction, $this>
     */
    public function acquiringTransaction(): HasOne
    {
        $acquiring = array_map(
            fn (TransactionType $type): string => $type->value,
            array_filter(TransactionType::cases(), fn (TransactionType $type): bool => $type->acquires()),
        );

        // The constraint goes through ofMany's second argument rather than a
        // plain where on the relation: the aggregate runs in its own subquery,
        // and a constraint outside it would pick the earliest transaction of any
        // type and then discard it for not being an acquiring one.
        return $this->hasOne(Transaction::class)->ofMany(
            ['occurred_at' => 'min', 'id' => 'min'],
            fn (Builder $query) => $query->whereIn('type', $acquiring),
        );
    }

    /**
     * Get every valuation recorded against the copy, most recent first.
     *
     * @return HasMany<Valuation, $this>
     */
    public function valuations(): HasMany
    {
        return $this->hasMany(Valuation::class)->orderByDesc('valued_at')->orderByDesc('id');
    }

    /**
     * Get the valuation the current estimated value is read from.
     *
     * Valuations are append-only, so the current figure is simply the most
     * recent one. The id breaks ties, so valuing a copy twice on the same day
     * still reads the second of the two rather than picking arbitrarily.
     *
     * @return HasOne<Valuation, $this>
     */
    public function latestValuation(): HasOne
    {
        return $this->hasOne(Valuation::class)->ofMany([
            'valued_at' => 'max',
            'id' => 'max',
        ]);
    }

    /**
     * Get what the copy is currently reckoned to be worth, in cents.
     *
     * Null means nobody has valued it yet, which is not the same as it being
     * worth nothing, so callers summing values must not coalesce it to zero
     * without meaning to.
     */
    public function estimatedValue(): ?int
    {
        return $this->latestValuation?->amount;
    }

    /**
     * Get when the copy entered the collection.
     *
     * Null means it has no transaction saying how it was acquired, not that it
     * arrived at an unknown date.
     */
    public function acquiredAt(): ?Carbon
    {
        return $this->acquiringTransaction?->occurred_at;
    }

    /**
     * Get what was paid to acquire the copy, in cents.
     *
     * This is the total of the acquiring transaction, so it includes the tax,
     * the fees and the shipping that came with it rather than the headline
     * price alone.
     */
    public function pricePaid(): ?int
    {
        return $this->acquiringTransaction?->total();
    }
}
