<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ItemPhotoSearchToken
 *
 * One searchable hash of one photo, produced by App\Services\BlindIndex from the
 * file name of the photo and the name of the item it belongs to. A photo has as
 * many of these as it has words and prefixes of words.
 *
 * @property int $id
 * @property int $item_photo_id
 * @property string $token
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class ItemPhotoSearchToken extends Model
{
    protected $table = 'item_photo_search_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_photo_id',
        'token',
    ];

    /**
     * Get the photo the token makes searchable.
     *
     * @return BelongsTo<ItemPhoto, $this>
     */
    public function itemPhoto(): BelongsTo
    {
        return $this->belongsTo(ItemPhoto::class);
    }
}
