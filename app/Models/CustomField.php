<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FieldTypeEnum;
use App\Models\Concerns\HasAuthor;
use Carbon\Carbon;
use Database\Factories\CustomFieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class CustomField
 *
 * @property int $id
 * @property int $type_id
 * @property int|null $group_id
 * @property string $name
 * @property FieldTypeEnum $field_type
 * @property array<int, mixed>|null $options
 * @property int $position
 * @property int|null $created_by_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_id
 * @property string|null $updated_by_name
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class CustomField extends Model
{
    use HasAuthor;

    /** @use HasFactory<CustomFieldFactory> */
    use HasFactory;

    protected $table = 'custom_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type_id',
        'group_id',
        'name',
        'field_type',
        'options',
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
            'field_type' => FieldTypeEnum::class,
            'options' => 'array',
            'position' => 'integer',
        ];
    }

    /**
     * Get the collection type the field is attached to.
     *
     * @return BelongsTo<CollectionType, $this>
     */
    public function collectionType(): BelongsTo
    {
        return $this->belongsTo(CollectionType::class, 'type_id');
    }

    /**
     * Get the group the field sits in, if any.
     *
     * @return BelongsTo<CustomFieldGroup, $this>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(CustomFieldGroup::class, 'group_id');
    }
}
