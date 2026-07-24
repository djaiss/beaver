<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\IndexSearchable;
use App\Contracts\Searchable;
use App\Traits\HasAuthor;
use App\Traits\HasDeleter;
use App\Traits\HasSearchIndex;
use Carbon\Carbon;
use Database\Factories\SetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Set
 *
 * A group of items collected together as a series, e.g. "Amazing Spider-Man
 * #1-10". Used to track completion, which items in the set are owned versus
 * still needed.
 *
 * @property int $id
 * @property int $catalog_id
 * @property string $name
 * @property string|null $description
 * @property int|null $target_count
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property int|null $deleted_by_id
 * @property string|null $deleted_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $items_count
 */
class Set extends Model implements Searchable
{
    use HasAuthor;
    use HasDeleter;

    /** @use HasFactory<SetFactory> */
    use HasFactory;

    use HasSearchIndex;
    use SoftDeletes;

    protected $table = 'sets';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'catalog_id',
        'name',
        'description',
        'target_count',
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
            'target_count' => 'integer',
        ];
    }

    /**
     * Get the collection the set belongs to.
     *
     * @return BelongsTo<Catalog, $this>
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    /**
     * Get the items that are part of this set.
     *
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function searchableAccountId(): ?int
    {
        return $this->catalog?->account_id;
    }

    /**
     * @return array<int, list<string>>
     */
    public function searchableText(): array
    {
        return [
            IndexSearchable::WEIGHT_TITLE => [$this->name],
            IndexSearchable::WEIGHT_RELATED => [(string) $this->catalog?->name],
            IndexSearchable::WEIGHT_TEXT => [(string) $this->description],
        ];
    }

    public function searchableTitle(): string
    {
        return $this->name;
    }

    public function searchableContext(): string
    {
        $owned = $this->items_count ?? $this->items()->count();

        if ($this->target_count === null || $this->target_count === 0) {
            return trans_choice(':count item|:count items', $owned, ['count' => $owned]);
        }

        return __(':owned of :target items', ['owned' => $owned, 'target' => $this->target_count]);
    }

    /**
     * Sets have no screen of their own, so a result opens the list of the
     * collection and jumps to the card.
     */
    public function searchableUrl(): string
    {
        return route('sets.index', $this->catalog_id).'#set-'.$this->id;
    }

    public function searchableCollectionName(): ?string
    {
        return $this->catalog?->name;
    }

    /**
     * @return iterable<int, Model>
     */
    public function searchableDependents(): iterable
    {
        return $this->items()->get();
    }
}
