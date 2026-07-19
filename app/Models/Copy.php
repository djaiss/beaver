<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CopyStatus;
use App\Models\Concerns\HasAuthor;
use App\Models\Concerns\HasDeleter;
use Carbon\Carbon;
use Database\Factories\CopyFactory;
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
}
