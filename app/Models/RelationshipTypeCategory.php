<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\RelationshipTypeCategoryFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $vault_id
 * @property string $key
 * @property string|null $name
 * @property string|null $name_translation_key
 * @property int $position
 * @property bool $can_be_deleted
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
class RelationshipTypeCategory extends Model
{
    /** @use HasFactory<RelationshipTypeCategoryFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relationship_type_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vault_id',
        'key',
        'name',
        'name_translation_key',
        'position',
        'can_be_deleted',
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
            'position' => 'integer',
            'can_be_deleted' => 'boolean',
        ];
    }

    /**
     * Get the relationship type category name.
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

    /** @return BelongsTo<Vault, $this> */
    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class);
    }
}
