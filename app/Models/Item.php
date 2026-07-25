<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\IndexSearchable;
use App\Contracts\Searchable;
use App\Traits\HasAuthor;
use App\Traits\HasDeleter;
use App\Traits\HasSearchIndex;
use Carbon\Carbon;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Item
 *
 * A catalog entry for a kind of object, e.g. Amazing Spider-Man #1.
 * An item reaches its account through its collection.
 *
 * @property int $id
 * @property int $catalog_id
 * @property int|null $category_id
 * @property int|null $type_id
 * @property int|null $set_id
 * @property string $name
 * @property string|null $description
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property int|null $deleted_by_id
 * @property string|null $deleted_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $copies_count
 */
class Item extends Model implements Searchable
{
    use HasAuthor;
    use HasDeleter;

    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    use HasSearchIndex;
    use SoftDeletes;

    protected $table = 'items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'catalog_id',
        'category_id',
        'type_id',
        'set_id',
        'series_id',
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
     * Get the collection the item belongs to.
     *
     * @return BelongsTo<Catalog, $this>
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    /**
     * Get the type of the item, if any.
     *
     * @return BelongsTo<CatalogType, $this>
     */
    public function catalogType(): BelongsTo
    {
        return $this->belongsTo(CatalogType::class, 'type_id');
    }

    /**
     * Get the category the item sits in, if any.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the set the item is part of, if any.
     *
     * @return BelongsTo<Set, $this>
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class);
    }

    /**
     * Get the series the item belongs to, if any. A series is account-wide, so it
     * may gather items from collections other than this one.
     *
     * @return BelongsTo<Series, $this>
     */
    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    /**
     * Get the tags applied to the item.
     *
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the custom field values recorded for the item.
     *
     * @return HasMany<CustomFieldValue, $this>
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    /**
     * Get the photos of the item, in the order the user arranged them.
     *
     * @return HasMany<ItemPhoto, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ItemPhoto::class)->orderBy('position')->orderBy('id');
    }

    /**
     * Get the main visual of the item. An item with photos always has exactly
     * one, and an item without photos has none.
     *
     * @return HasOne<ItemPhoto, $this>
     */
    public function mainPhoto(): HasOne
    {
        return $this->hasOne(ItemPhoto::class)->where('is_main', true);
    }

    /**
     * Get the physical copies owned of the item.
     *
     * @return HasMany<Copy, $this>
     */
    public function copies(): HasMany
    {
        return $this->hasMany(Copy::class);
    }

    /**
     * Get the activity trail of the item.
     *
     * @return HasMany<ItemLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ItemLog::class);
    }

    public function searchableAccountId(): ?int
    {
        return $this->catalog?->account_id;
    }

    /**
     * An item is the thing people actually look for, so it carries the widest
     * net: everything filed around it is indexed into it, which is how searching
     * a tag or a category name surfaces the items rather than only the label.
     *
     * @return array<int, list<string>>
     */
    public function searchableText(): array
    {
        return [
            IndexSearchable::WEIGHT_TITLE => [$this->name],
            IndexSearchable::WEIGHT_RELATED => [
                (string) $this->catalog?->name,
                (string) $this->category?->name,
                (string) $this->set?->name,
                (string) $this->series?->name,
                (string) $this->catalogType?->name,
                ...$this->tags->pluck('name')->all(),
            ],
            IndexSearchable::WEIGHT_TEXT => [
                (string) $this->description,
                ...$this->customFieldValues->pluck('value')->all(),
            ],
        ];
    }

    public function searchableTitle(): string
    {
        return $this->name;
    }

    public function searchableContext(): string
    {
        $copies = $this->copies_count ?? $this->copies()->count();

        return trans_choice(':count copy|:count copies', $copies, ['count' => $copies]);
    }

    public function searchableUrl(): string
    {
        return route('items.show', [$this->catalog_id, $this->id]);
    }

    public function searchableThumbnailUrl(): ?string
    {
        return $this->mainPhoto?->url();
    }

    public function searchableCollectionName(): ?string
    {
        return $this->catalog?->name;
    }

    /**
     * @return iterable<int, Model&Searchable>
     */
    public function searchableDependents(): iterable
    {
        $copies = $this->copies()->with(['loans', 'documents'])->get();

        return $copies
            ->concat($this->photos()->get())
            ->concat($copies->flatMap->loans)
            ->concat($copies->flatMap->documents);
    }
}
