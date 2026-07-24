<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class SearchToken
 *
 * One searchable hash of one record, produced by App\Services\BlindIndex from the
 * text of that record and of the few things around it a user would search it by.
 * A record has as many of these as it has words and prefixes of words.
 *
 * The account is carried on the row rather than reached through the record, so a
 * search never leaves the tokens table until it knows what it matched.
 *
 * @property int $id
 * @property int $account_id
 * @property string $searchable_type
 * @property int $searchable_id
 * @property string $token
 * @property int $weight
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Account $account
 */
class SearchToken extends Model
{
    protected $table = 'search_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'searchable_type',
        'searchable_id',
        'token',
        'weight',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight' => 'integer',
        ];
    }

    /**
     * Get the account the token belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the record the token makes searchable.
     *
     * @return MorphTo<Model, $this>
     */
    public function searchable(): MorphTo
    {
        return $this->morphTo();
    }
}
