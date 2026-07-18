<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\CustomFieldValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CustomFieldValue
 *
 * The value of a custom field for a specific item, e.g. the issue number of a
 * comic.
 *
 * @property int $id
 * @property int $item_id
 * @property int $custom_field_id
 * @property string|null $value
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class CustomFieldValue extends Model
{
    /** @use HasFactory<CustomFieldValueFactory> */
    use HasFactory;

    protected $table = 'custom_field_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'item_id',
        'custom_field_id',
        'value',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'encrypted',
        ];
    }

    /**
     * Get the item the value belongs to.
     *
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the field the value answers.
     *
     * @return BelongsTo<CustomField, $this>
     */
    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }
}
