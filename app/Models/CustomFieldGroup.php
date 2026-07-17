<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\CustomFieldGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CustomFieldGroup
 *
 * A named section of custom fields within a type, e.g. "Main" holding the
 * subtitle and author, then "Details" holding the publisher information.
 * Groups keep the item form readable once a type carries many fields.
 *
 * @property int $id
 * @property int $type_id
 * @property string $name
 * @property int $position
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class CustomFieldGroup extends Model
{
    use HasAuthor;

    /** @use HasFactory<CustomFieldGroupFactory> */
    use HasFactory;

    protected $table = 'custom_field_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type_id',
        'name',
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
            'name' => 'encrypted',
            'position' => 'integer',
        ];
    }

    /**
     * Get the collection type the group belongs to.
     *
     * @return BelongsTo<CollectionType, $this>
     */
    public function collectionType(): BelongsTo
    {
        return $this->belongsTo(CollectionType::class, 'type_id');
    }

    /**
     * Get the custom fields sitting in the group.
     *
     * @return HasMany<CustomField, $this>
     */
    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class, 'group_id');
    }
}
