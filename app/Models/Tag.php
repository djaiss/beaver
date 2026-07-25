<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\IndexSearchable;
use App\Contracts\Searchable;
use App\Traits\HasAuthor;
use App\Traits\HasSearchIndex;
use Carbon\Carbon;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property int|null $items_count
 */
class Tag extends Model implements Searchable
{
    use HasAuthor;

    /** @use HasFactory<TagFactory> */
    use HasFactory;

    use HasSearchIndex;

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

    /**
     * Get the items that carry this tag.
     *
     * @return BelongsToMany<Item, $this>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    public function searchableAccountId(): ?int
    {
        return $this->account_id;
    }

    /**
     * @return array<int, list<string>>
     */
    public function searchableText(): array
    {
        return [
            IndexSearchable::WEIGHT_TITLE => [$this->name],
        ];
    }

    public function searchableTitle(): string
    {
        return $this->name;
    }

    public function searchableContext(): string
    {
        $items = $this->items_count ?? $this->items()->count();

        return trans_choice(':count item|:count items', $items, ['count' => $items]);
    }

    /**
     * Tags are only managed, never browsed, so the one screen that shows them is
     * for owners and editors. Account search leaves tag results out for a
     * viewer rather than sending them somewhere they cannot open.
     */
    public function searchableUrl(): string
    {
        return route('settings.tags.index').'#tag-'.$this->id;
    }

    /**
     * @return iterable<int, Model&Searchable>
     */
    public function searchableDependents(): iterable
    {
        return $this->items()->get();
    }
}
