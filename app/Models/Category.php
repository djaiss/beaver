<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\IndexSearchable;
use App\Contracts\Searchable;
use App\Traits\HasAuthor;
use App\Traits\HasDeleter;
use App\Traits\HasSearchIndex;
use Carbon\Carbon;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 *
 * A category groups items within a collection, e.g. "Spider-Man" within a
 * comics collection. Categories can be nested.
 *
 * @property int $id
 * @property int $catalog_id
 * @property int|null $parent_id
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
 * @property int|null $items_count
 */
class Category extends Model implements Searchable
{
    use HasAuthor;
    use HasDeleter;

    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    use HasSearchIndex;
    use SoftDeletes;

    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'catalog_id',
        'parent_id',
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
     * Get the collection the category belongs to.
     *
     * @return BelongsTo<Catalog, $this>
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    /**
     * Get the parent category, if this one is nested.
     *
     * @return BelongsTo<Category, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the direct child categories.
     *
     * @return HasMany<Category, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the items filed under this category.
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
            IndexSearchable::WEIGHT_RELATED => [
                (string) $this->catalog?->name,
                (string) $this->parent?->name,
            ],
            IndexSearchable::WEIGHT_TEXT => [(string) $this->description],
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

    public function searchableUrl(): string
    {
        return route('categories.show', [$this->catalog_id, $this->id]);
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
