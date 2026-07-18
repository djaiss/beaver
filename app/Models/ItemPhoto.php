<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\ItemPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * The URL that streams the photo file through the app.
     */
    public function url(): string
    {
        return route('items.photos.show', $this);
    }
}
