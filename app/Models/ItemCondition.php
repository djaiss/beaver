<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\ItemConditionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ItemCondition
 *
 * The state of an item, e.g. "New", "Used", "Damaged", belonging to an
 * account. A set of defaults is seeded with a null account, and accounts
 * can add their own.
 *
 * @property int $id
 * @property int|null $account_id
 * @property string $name
 * @property int $position
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class ItemCondition extends Model
{
    use HasAuthor;

    /** @use HasFactory<ItemConditionFactory> */
    use HasFactory;

    protected $table = 'item_conditions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'name',
        'position',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'position' => 'integer',
        ];
    }

    /**
     * Get the account the condition belongs to, if it is not a system default.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the copies that carry this condition.
     *
     * @return HasMany<Copy, $this>
     */
    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }

    /**
     * Whether the condition is a system default, shared across all accounts.
     */
    public function isSystemDefault(): bool
    {
        return $this->account_id === null;
    }

    /**
     * Whether this condition ranks worse than another. Conditions are ordered
     * best to worst by position, so a higher position is a worse state. Used to
     * flag a copy that came back from a loan in worse shape than it left.
     */
    public function isWorseThan(ItemCondition $other): bool
    {
        return $this->position > $other->position;
    }
}
