<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use App\Models\Concerns\HasDeleter;
use Carbon\Carbon;
use Database\Factories\CopyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Copy
 *
 * A single physical instance of an item. An item owned three times has three
 * copies, each with its own condition, location, and acquisition details.
 *
 * @property int $id
 * @property int $item_id
 * @property int|null $condition_id
 * @property int|null $location_id
 * @property Carbon|null $acquired_at
 * @property int|null $price_paid
 * @property int|null $estimated_value
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
class Copy extends Model
{
    use HasAuthor;
    use HasDeleter;

    /** @use HasFactory<CopyFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'copies';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_id',
        'condition_id',
        'location_id',
        'acquired_at',
        'price_paid',
        'estimated_value',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'acquired_at' => 'date',
            'price_paid' => 'integer',
            'estimated_value' => 'integer',
        ];
    }

    /**
     * Get the item this is a copy of.
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the condition of the copy, if any.
     *
     * @return BelongsTo<Condition, $this>
     */
    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    /**
     * Get the location where the copy is stored, if any.
     *
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
