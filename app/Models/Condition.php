<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\ConditionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Condition
 *
 * The state of an item, e.g. "New", "Used", "Damaged", belonging to an
 * account. A set of defaults is seeded with a null account, and accounts
 * can add their own.
 *
 * @property int $id
 * @property int|null $account_id
 * @property string $name
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Condition extends Model
{
    use HasAuthor;

    /** @use HasFactory<ConditionFactory> */
    use HasFactory;

    protected $table = 'conditions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'name',
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
     * Whether the condition is a system default, shared across all accounts.
     */
    public function isSystemDefault(): bool
    {
        return $this->account_id === null;
    }
}
