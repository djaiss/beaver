<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Tag
 *
 * A free-form label an item can have, e.g. "Signed", "First Issue".
 * Reusable across all collections in an account.
 *
 * @property int $id
 * @property int $account_id
 * @property string $name
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class Tag extends Model
{
    use HasAuthor;

    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected $table = 'tags';

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
     * Get the account the tag belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
