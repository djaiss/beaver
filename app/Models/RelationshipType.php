<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\RelationshipTypeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vault_id
 * @property int $relationship_type_category_id
 * @property string $key
 * @property string $name
 * @property string|null $name_translation_key
 * @property string|null $forward_name_translation_key
 * @property string|null $reverse_name_translation_key
 * @property bool $is_directed
 * @property bool $can_be_deleted
 * @property int $position
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class RelationshipType extends Model
{
    /** @use HasFactory<RelationshipTypeFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relationship_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vault_id',
        'relationship_type_category_id',
        'key',
        'name',
        'name_translation_key',
        'forward_name_translation_key',
        'reverse_name_translation_key',
        'is_directed',
        'can_be_deleted',
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
            'name_translation_key' => 'encrypted',
            'forward_name_translation_key' => 'encrypted',
            'reverse_name_translation_key' => 'encrypted',
            'is_directed' => 'boolean',
            'can_be_deleted' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * Get the relationship type name.
     *
     * @return Attribute<string, never>
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes): string {
                if ($value !== null) {
                    return $this->fromEncryptedString($value);
                }

                $translationKey = $attributes['name_translation_key'] ?? null;

                return __($translationKey !== null ? $this->fromEncryptedString($translationKey) : '');
            },
        );
    }

    /**
     * Get the vault associated with the relationship type.
     *
     * @return BelongsTo<Vault, $this>
     */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }

    /**
     * Get the relationship type category associated with the relationship type.
     *
     * @return BelongsTo<RelationshipTypeCategory, $this>
     */
    public function relationshipTypeCategory(): BelongsTo
    {
        return $this->belongsTo(RelationshipTypeCategory::class);
    }
}
