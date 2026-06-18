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
 * A relationship type defines the semantics of a relationship between two people.
 *
 * Examples:
 * * Parent / Child
 * * Manager / Direct Report
 * * Friend
 * * Spouse
 *
 * Relationship types may be directed or non-directed.
 *
 * When `is_directed` is false, both sides of the relationship have the same meaning:
 *
 * Alice ── friend ── Bob
 *
 * Both Alice and Bob are friends of each other, so
 * `forward_name` and `reverse_name` are typically identical.
 *
 * When `is_directed` is true, the meaning depends on the direction of the relationship:
 *
 * Alice ── parent ──▶ Bob
 *
 * Alice is the parent of Bob, and Bob is the child of Alice.
 *
 * In this case:
 * * `forward_name` describes the role of the source person (`parent`)
 * * `reverse_name` describes the role of the target person (`child`)
 *
 * The same pattern applies to relationships such as:
 *
 * manager ──▶ direct report
 * mentor ──▶ mentee
 * teacher ──▶ student
 * landlord ──▶ tenant
 *
 * The `*_translation_key` attributes are used to translate these labels and
 * allow relationship names to be localized independently of their stored keys.
 */

/**
 * @property int $id
 * @property int $vault_id
 * @property int $relationship_type_category_id
 * @property string $key
 * @property string $name
 * @property string|null $name_translation_key
 * @property string $forward_name
 * @property string|null $forward_name_translation_key
 * @property string $reverse_name
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
        'forward_name',
        'forward_name_translation_key',
        'reverse_name',
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
            'forward_name' => 'encrypted',
            'forward_name_translation_key' => 'encrypted',
            'reverse_name' => 'encrypted',
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
     * Get the forward relationship type name.
     *
     * @return Attribute<string, never>
     */
    protected function forwardName(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes): string {
                if ($value !== null) {
                    return $this->fromEncryptedString($value);
                }

                $translationKey = $attributes['forward_name_translation_key'] ?? null;

                return __($translationKey !== null ? $this->fromEncryptedString($translationKey) : '');
            },
        );
    }

    /**
     * Get the reverse relationship type name.
     *
     * @return Attribute<string, never>
     */
    protected function reverseName(): Attribute
    {
        return Attribute::make(
            get: function (?string $value, array $attributes): string {
                if ($value !== null) {
                    return $this->fromEncryptedString($value);
                }

                $translationKey = $attributes['reverse_name_translation_key'] ?? null;

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
