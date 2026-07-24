<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAuthor;
use Carbon\Carbon;
use Database\Factories\ItemPhotoFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ItemPhoto
 *
 * A photo of an item, e.g. the cover of Amazing Spider-Man #1.
 * An item photo reaches its account through its item's collection.
 *
 * @property int $id
 * @property int $item_id
 * @property string $path
 * @property string $filename
 * @property string $mime_type
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property bool $is_main
 * @property int $position
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class ItemPhoto extends Model
{
    use HasAuthor;

    /** @use HasFactory<ItemPhotoFactory> */
    use HasFactory;

    protected $table = 'item_photos';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_id',
        'path',
        'filename',
        'mime_type',
        'size',
        'width',
        'height',
        'is_main',
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
            'filename' => 'encrypted',
            'is_main' => 'boolean',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'position' => 'integer',
        ];
    }

    /**
     * Get the item the photo belongs to.
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the search hashes that make the photo findable.
     *
     * @return HasMany<ItemPhotoSearchToken, $this>
     */
    public function searchTokens(): HasMany
    {
        return $this->hasMany(ItemPhotoSearchToken::class);
    }

    /**
     * Only the photos of one account, reached the one way a photo has of
     * knowing which account it belongs to: through its item's collection.
     *
     * @param  Builder<ItemPhoto>  $query
     * @return Builder<ItemPhoto>
     */
    #[Scope]
    protected function ofAccount(Builder $query, Account $account): Builder
    {
        return $query->whereHas(
            'item.catalog',
            fn (Builder $catalog): Builder => $catalog->where('account_id', $account->id),
        );
    }

    /**
     * The URL that streams the photo file through the app.
     */
    public function url(): string
    {
        return route('items.photos.show', $this);
    }

    /**
     * The dimensions of the image, or null for a photo uploaded before they
     * were recorded and not rebuilt since.
     */
    public function dimensions(): ?string
    {
        if ($this->width === null || $this->height === null) {
            return null;
        }

        return $this->width.' × '.$this->height;
    }

    /**
     * The mime type reduced to the label a person recognises, e.g. "JPEG".
     */
    public function format(): string
    {
        $separator = mb_strpos($this->mime_type, '/');

        if ($separator === false) {
            return mb_strtoupper($this->mime_type);
        }

        return mb_strtoupper(mb_substr($this->mime_type, $separator + 1));
    }
}
