<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ItemViewEnum;
use Carbon\Carbon;
use Database\Factories\CollectionViewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CollectionView
 *
 * One user's remembered items view (grid, list or table) for one collection.
 *
 * @property int $id
 * @property int $user_id
 * @property int $collection_id
 * @property ItemViewEnum $items_view
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class CollectionView extends Model
{
    /** @use HasFactory<CollectionViewFactory> */
    use HasFactory;

    protected $table = 'collection_views';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'collection_id',
        'items_view',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'items_view' => ItemViewEnum::class,
        ];
    }

    /**
     * Get the user the preference belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the collection the preference applies to.
     *
     * @return BelongsTo<Collection, $this>
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
