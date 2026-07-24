<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ItemViewEnum;
use App\Enums\VisibilityEnum;
use App\Traits\HasAuthor;
use App\Traits\HasDeleter;
use Carbon\Carbon;
use Database\Factories\CatalogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class Catalog
 *
 * @property int $id
 * @property string $uuid
 * @property int $account_id
 * @property string $name
 * @property string|null $description
 * @property string|null $emoji
 * @property VisibilityEnum $visibility
 * @property string|null $currency
 * @property array<string, mixed>|null $settings
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
class Catalog extends Model
{
    use HasAuthor;
    use HasDeleter;

    /** @use HasFactory<CatalogFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'catalogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_id',
        'name',
        'description',
        'emoji',
        'visibility',
        'currency',
        'settings',
    ];

    protected static function booted(): void
    {
        static::creating(function (Catalog $catalog): void {
            $catalog->uuid ??= (string) Str::uuid();
        });
    }

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
            'visibility' => VisibilityEnum::class,
            'settings' => 'array',
        ];
    }

    /**
     * Get the account the collection belongs to.
     *
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the collection types linked to the collection.
     *
     * @return BelongsToMany<CatalogType, $this>
     */
    public function catalogTypes(): BelongsToMany
    {
        return $this->belongsToMany(CatalogType::class, 'catalog_type', 'catalog_id', 'type_id');
    }

    /**
     * Get the items catalogued in the collection.
     *
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Get the categories that group the collection's items.
     *
     * @return HasMany<Category, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * Get the sets being tracked within the collection.
     *
     * @return HasMany<Set, $this>
     */
    public function sets(): HasMany
    {
        return $this->hasMany(Set::class);
    }

    /**
     * Get the remembered view preferences, one per user who has opened the collection.
     *
     * @return HasMany<CatalogView, $this>
     */
    public function catalogViews(): HasMany
    {
        return $this->hasMany(CatalogView::class);
    }

    /**
     * The items view the given user last opened for this collection, defaulting to the grid.
     */
    public function viewForUser(User $user): ItemViewEnum
    {
        return $this->catalogViews()
            ->where('user_id', $user->id)
            ->value('items_view') ?? ItemViewEnum::Grid;
    }
}
