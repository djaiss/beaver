<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAuthor;
use Carbon\Carbon;
use Database\Factories\SeriesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Series
 *
 * A broader grouping of items that may span several collections, e.g. "Harry
 * Potter" holding books, films and LEGO sets. Shared across the account.
 *
 * Unlike a Set, a series has no target and no notion of completion. It answers
 * "what larger franchise does this item belong to?", where a set answers "which
 * finite list is this item part of?".
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property string|null $description
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Series extends Model
{
    use HasAuthor;

    /** @use HasFactory<SeriesFactory> */
    use HasFactory;

    protected $table = 'series';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'name',
        'description',
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
            'description' => 'encrypted',
        ];
    }

    /**
     * Get the account the series belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the items linked to this series, across every collection of the account.
     *
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
